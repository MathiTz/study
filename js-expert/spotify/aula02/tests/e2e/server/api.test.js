import { jest, expect, describe, test, beforeEach } from "@jest/globals";
import config from "../../../server/config.js";
import Server from "../../../server/server.js";
const {
  pages: { controllerHTML, homeHTML },
  location,
  constants: { CONTENT_TYPE },
} = config;

import superTest from "supertest";
import portfinder from "portfinder";
import { Transform } from "stream";
import { setTimeout } from "timers/promises";

const getAvailablePort = portfinder.getPortPromise;
const RETETION_DATA_PERIOD = 200;
describe("API E2E Suite Test", () => {
  function pipeAndReadStreamData(stream, onChunk) {
    const transform = new Transform({
      transform(chunk, ec, cb) {
        onChunk(chunk);
        cb(null, chunk);
      },
    });
    return stream.pipe(transform);
  }

  describe("client workflow", () => {
    async function getTestServer() {
      const getSuperTest = (port) => superTest(`http://localhost:${port}`);
      const port = await getAvailablePort();

      return new Promise((resolve, reject) => {
        const server = Server.listen(port)
          .once("listening", () => {
            const testServer = getSuperTest(port);
            const response = {
              testServer,
              kill() {
                server.close();
              },
            };

            return resolve(response);
          })
          .once("error", reject);
      });
    }

    test("it should not receive data string if the process is not playing", async () => {
      const server = await getTestServer();
      const onChunk = jest.fn();
      pipeAndReadStreamData(server.testServer.get("/stream"), onChunk);

      await setTimeout(RETETION_DATA_PERIOD);
      server.kill();
      expect(onChunk).not.toHaveBeenCalled();
    });
    test.todo("it should receive data string if the process is playing");
  });
});
