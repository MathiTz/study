import { jest, expect, describe, test } from "@jest/globals";
import config from "../../../server/config.js";
import TestUtil from "../_util/testUtil.js";
import { Controller } from "../../../server/controller.js";
import { Service } from "../../../server/service.js";
import fs from "fs";

const { pages } = config;

describe("#Controller - test suite for controller return", () => {
  beforeEach(() => {
    jest.restoreAllMocks();
    jest.clearAllMocks();
  });

  test("getFileStream() - should return a fileStream", async () => {
    const controller = new Controller();
    const mockFileStream = TestUtil.generateReadableStream(["test"]);

    const jestMock = jest
      .spyOn(Service.prototype, Service.prototype.getFileStream.name)
      .mockResolvedValue({
        stream: mockFileStream,
        type: "text/html",
      });

    await controller.getFileStream(pages.homeHTML);

    expect(jestMock).toBeCalledWith(pages.homeHTML);
  });

  test("getFileStream() - should return a fileStream with certain type", async () => {
    const controller = new Controller();
    const expectedType = ".html";

    const { stream, type } = await controller.getFileStream(pages.homeHTML);

    expect(stream).toBeInstanceOf(fs.ReadStream);
    expect(type).toBe(expectedType);
  });
});
