import { render, screen } from "@testing-library/react";
import { useSession } from "next-auth/client";
import PostPreview, { getStaticProps } from "../../pages/posts/preview/[slug]";
import { mockFunction } from "../mockFunction";
import { getPrismicClient } from "../../services/prismic";
import { useRouter } from "next/router";

jest.mock("next/router");
jest.mock("next-auth/client");
jest.mock("react", () => ({
  ...jest.requireActual("react"),
  useEffect: (f) => f(),
}));

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

describe("Post preview page", () => {
  let getPrismicClientMocked: jest.MockedFunction<typeof getPrismicClient>;
  let useSessionMocked: jest.MockedFunction<typeof useSession>;
  let useRouterMocked: jest.MockedFunction<typeof useRouter>;

  beforeEach(() => {
    getPrismicClientMocked = mockFunction(getPrismicClient);
    useSessionMocked = mockFunction(useSession);
    useRouterMocked = mockFunction(useRouter);
  });

  it("renders correctly", () => {
    useSessionMocked.mockReturnValueOnce([null, false]);
    render(PostPreview({ post }));

    expect(screen.getByText("My New Post")).toBeInTheDocument();
    expect(screen.getByText("Post content")).toBeInTheDocument();
    expect(screen.getByText("Wanna continue reading?")).toBeInTheDocument();
  });

  it("redirects user to full post when user is subscribed", async () => {
    const pushMock = jest.fn();
    useSessionMocked.mockReturnValueOnce([
      { activeSubscription: "fake-active-subscription" },
      false,
    ]);
    useRouterMocked.mockReturnValueOnce({
      push: pushMock,
    } as any);

    render(PostPreview({ post }));

    expect(pushMock).toHaveBeenCalledWith("/posts/my-new-post");
  });

  it("loads initial data", async () => {
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

    const response = await getStaticProps({
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
