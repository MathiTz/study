import {
  render,
  screen,
  waitFor,
  waitForElementToBeRemoved,
} from "@testing-library/react";
import { Async } from ".";

describe("Async component", () => {
  it("renders correctly", async () => {
    render(<Async />);

    expect(screen.getByText("Hello World")).toBeInTheDocument();

    // await waitFor(() => {
    //   return expect(screen.getByText("Button")).toBeInTheDocument();
    // });

    await waitForElementToBeRemoved(screen.queryByText("Button"));
  });
});
