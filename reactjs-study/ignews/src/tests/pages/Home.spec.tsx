import { render, screen } from "@testing-library/react";
import { stripe } from "../../services/stripe";
import { useSession } from "next-auth/client";
import Home, { getStaticProps } from "../../pages";
import { mockFunction } from "../mockFunction";

jest.mock("next/router");
jest.mock("next-auth/client");
jest.mock("../../services/stripe");

describe("Home page", () => {
  let useSessionMocked: jest.MockedFunction<typeof useSession>;
  let stripeMocked: jest.MockedFunction<typeof stripe.prices.retrieve>;

  beforeEach(() => {
    useSessionMocked = mockFunction(useSession);
    stripeMocked = mockFunction(stripe.prices.retrieve);
  });

  it("renders correctly", () => {
    useSessionMocked.mockReturnValueOnce([null, false]);
    render(<Home product={{ priceId: "fake-price-id", amount: "R$10,00" }} />);

    expect(screen.getByText("for R$10,00 month")).toBeInTheDocument();
  });

  it("loads initial data", async () => {
    stripeMocked.mockResolvedValueOnce({
      id: "fake-price-id",
      unit_amount: 1000,
    } as any);

    const response = await getStaticProps({});

    expect(response).toEqual(
      expect.objectContaining({
        props: {
          product: {
            id: "fake-price-id",
            amount: "$10.00",
          },
        },
      })
    );
  });
});
