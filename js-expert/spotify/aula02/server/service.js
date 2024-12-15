import fs from "fs";
import fsPromises from "fs/promises";
import { randomUUID } from "crypto";
import { PassThrough, Writable } from "stream";
import { once } from "events";
import streamsPromises from "stream/promises";
import Throttle from "throttle";
import childProcess from "child_process";
import config from "./config.js";
import { extname, join } from "path";
import { logger } from "./util.js";

const {
  dir: { publicDirectory },
  constants: { fallbackBitRate, englishConversation, bitRateDivisor },
} = config;

export class Service {
  constructor() {
    this.clietStreams = new Map();
    this.currentSong = englishConversation;
    this.currentBitRate = 0;
    this.throttleTransform = {};
    this.currentReadable = {};
  }

  createClientStream() {
    const id = randomUUID();
    const clientStream = new PassThrough();
    this.clietStreams.set(id, clientStream);

    return {
      id,
      clientStream,
    };
  }

  removeClientStream(id) {
    this.clietStreams.delete(id);
  }

  _executeSoxCommand(commands) {
    return childProcess.spawn("sox", commands);
  }

  async getBitRate(song) {
    try {
      const args = [
        "--i", // info
        "-B", // bitrate
        song,
      ];

      const {
        stderr, // tudo que é erro
        stdout, // tudo que é log
        stdin, // enviar dados como stream
      } = this._executeSoxCommand(args);

      await Promise.all([once(stderr), once(stdout)]);

      const [success, error] = [stdout, stderr].map((stream) => stream.read());
      if (error) return await Promise.reject(error);

      return success.toString().trim().replace(/k/, "000");
    } catch (error) {
      logger.error(`deu ruim no bitrate: ${error}`);

      return fallbackBitRate;
    }
  }

  broadCast() {
    return new Writable({
      write: (chunk, encoding, callback) => {
        for (const [id, stream] of this.clietStreams) {
          // se o cliente desconectou não devemos mais mandar para ele
          if (stream.writableEnded) {
            this.clietStreams.delete(id);
            continue;
          }

          stream.write(chunk);
        }

        callback();
      },
    });
  }

  async startStreaming() {
    logger.info(`starting with ${this.currentSong}`);
    const bitrate = (this.currentBitRate =
      (await this.getBitRate(this.currentSong)) / bitRateDivisor);

    const throttleTransform = (this.throttleTransform = new Throttle(bitrate));
    const songReadable = (this.currentReadable = this.createFileStream(
      this.currentSong
    ));

    return streamsPromises.pipeline(
      songReadable,
      throttleTransform,
      this.broadCast()
    );
  }

  stopStreaming() {
    this.throttleTransform?.end?.();
  }

  createFileStream(filename) {
    return fs.createReadStream(filename);
  }

  async getFileInfo(file) {
    // file = home/index.html
    const fullFilePath = join(publicDirectory, file);

    // valida se exista, se não existe estoure erro
    await fsPromises.access(fullFilePath);

    const fileType = extname(fullFilePath);
    return {
      type: fileType,
      name: fullFilePath,
    };
  }

  async getFileStream(file) {
    const { name, type } = await this.getFileInfo(file);

    return {
      stream: this.createFileStream(name),
      type,
    };
  }
}
