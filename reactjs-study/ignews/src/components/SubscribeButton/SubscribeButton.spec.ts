import { render, screen, fireEvent } from "@testing-library/react";
import { signIn, useSession } from "next-auth/client";
import { useRouter } from "next/router";
import { SubscribeButton } from ".";
import { mockFunction } from "../../tests/mockFunction";

jest.mock("next-auth/client");

jest.mock("next/router");

describe("SubscribeButton component", () => {
  let useSessionMocked: jest.MockedFunction<typeof useSession>;
  let signInMocked: jest.MockedFunction<typeof signIn>;
  let useRouterMocked: jest.MockedFunction<typeof useRouter>;

  beforeEach(() => {
    useSessionMocked = mockFunction(useSession);
    signInMocked = mockFunction(signIn);
    useRouterMocked = mockFunction(useRouter);
  });

  it("renders correctly", () => {
    useSessionMocked.mockReturnValue([null, false]);
    render(SubscribeButton());

    expect(screen.getByText("Subscribe now")).toBeInTheDocument();
  });

  it("redirects user to sign in when not authenticated", () => {
    render(SubscribeButton());
    const subscribeButton = screen.getByText("Subscribe now");

    fireEvent.click(subscribeButton);

    expect(signInMocked).toHaveBeenCalled();
  });

  it("redirects to posts when user already hasa subscription", () => {
    useSessionMocked.mockReturnValue([
      {
        user: { name: "John Doe", email: "john.doe@example.com" },
        expires: "fake-expires",
        activeSubscription: "fake-active-subscription",
      },
      false,
    ]);

    useRouterMocked.mockReturnValue({
      push: jest.fn(),
    } as any);

    render(SubscribeButton());
    const subscribeButton = screen.getByText("Subscribe now");

    fireEvent.click(subscribeButton);
    expect(useRouterMocked().push).toHaveBeenCalled();
    expect(useRouterMocked().push).toHaveBeenCalledWith("/posts");
  });
});
