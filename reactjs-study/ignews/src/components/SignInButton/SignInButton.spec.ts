import { screen, render } from "@testing-library/react";
import { useSession } from "next-auth/client";
import { SignInButton } from ".";
import { mockFunction } from "../../tests/mockFunction";

jest.mock("next-auth/client");

describe("SignInButton component", () => {
  let useSessionMocked: jest.MockedFunction<typeof useSession>;

  beforeEach(() => {
    useSessionMocked = mockFunction(useSession);
  });

  it("renders correctly when user not authenticated", () => {
    useSessionMocked.mockReturnValueOnce([null, false]);

    render(SignInButton());

    expect(screen.getByText("Sign in with Github")).toBeInTheDocument();
  });

  it("renders correctly when user authenticated", () => {
    const useSessionMocked = mockFunction(useSession);
    useSessionMocked.mockReturnValueOnce([
      {
        user: { name: "John Doe", email: "john.doe@example.com" },
        expires: "fake-expires",
      },
      false,
    ]);

    render(SignInButton());

    expect(screen.getByText("John Doe")).toBeInTheDocument();
  });
});
