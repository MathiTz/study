import { render, screen } from "@testing-library/react";
import { getSession } from "next-auth/client";
import Post, { getServerSideProps } from "../../pages/posts/[slug]";
import { mockFunction } from "../mockFunction";
import { getPrismicClient } from "../../services/prismic";

jest.mock("next/router");
jest.mock("next-auth/client");

jest.mock("../../services/prismic");

type Post = {
  slug: string;
  title: string;
  content: string;
  updatedAt: string;
};

const post: Post = {
  slug: "my-new-post",
  title: "My New Post",
  content: "<p>Post content</p>",
  updatedAt: "March, 10",
};

describe("Post page", () => {
  let getPrismicClientMocked: jest.MockedFunction<typeof getPrismicClient>;
  let getSessionMocked: jest.MockedFunction<typeof getSession>;

  beforeEach(() => {
    getPrismicClientMocked = mockFunction(getPrismicClient);
    getSessionMocked = mockFunction(getSession);
  });

  it("renders correctly", () => {
    render(Post({ post }));

    expect(screen.getByText("My New Post")).toBeInTheDocument();
    expect(screen.getByText("Post content")).toBeInTheDocument();
  });

  it("redirects user if no subscription is found", async () => {
    getSessionMocked.mockResolvedValueOnce({
      activeSubscription: null,
    });

    const response = await getServerSideProps({
      params: {
        slug: "my-new-post",
      },
    } as any);

    expect(response).toEqual(
      expect.objectContaining({
        redirect: {
          destination: "/",
          permanent: false,
        },
      })
    );
  });

  it("loads initial data", async () => {
    getSessionMocked.mockResolvedValueOnce({
      activeSubscription: "fake-active-subscription",
    });

    getPrismicClientMocked.mockReturnValueOnce({
      getByUID: jest.fn().mockResolvedValueOnce({
        data: {
          title: [
            {
              type: "heading",
              text: "My new post",
            },
          ],
          content: [
            {
              type: "paragraph",
              text: "Post content",
            },
          ],
        },
        last_publication_date: "04-01-2021",
      }),
    } as any);

    const response = await getServerSideProps({
      params: {
        slug: "my-new-post",
      },
    } as any);

    expect(response).toEqual(
      expect.objectContaining({
        props: {
          post: {
            slug: "my-new-post",
            title: "My new post",
            content: "<p>Post content</p>",
            updatedAt: "01 de abril de 2021",
          },
        },
      })
    );
  });
});
