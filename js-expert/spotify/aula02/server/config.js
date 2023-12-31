import { join, dirname } from "path";
import { fileURLToPath } from "url";

const currentDir = dirname(fileURLToPath(import.meta.url));
const root = join(currentDir, "../");
const audioDirectory = join(root, "audio");
const publicDirectory = join(root, "public");
const songsDirectory = join(audioDirectory, "songs");
export default {
  port: process.env.PORT || 3000,
  dir: {
    root,
    audioDirectory,
    publicDirectory,
    songsDirectory,
    fxDirectory: join(audioDirectory, "fx"),
  },
  pages: {
    homeHTML: "home/index.html",
    controllerHTML: "controller/index.html",
  },
  location: {
    home: "/home",
  },
  constants: {
    CONTENT_TYPE: {
      ".html": "text/html",
      ".js": "text/javascript",
      ".css": "text/css",
    },
    audioMediaType: "mp3",
    songVolume: "0.99",
    fallbackBitRate: "128000",
    bitRateDivisor: 8,
    englishConversation: join(songsDirectory, "conversation.mp3"),
  },
};
