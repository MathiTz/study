import { render, screen } from "@testing-library/react";
import Posts, { getStaticProps } from "../../pages/posts";
import { mockFunction } from "../mockFunction";
import { getPrismicClient } from "../../services/prismic";

jest.mock("next/router");
jest.mock("next-auth/client");

jest.mock("../../services/prismic");

type Posts = {
  slug: string;
  title: string;
  excerpt: string;
  updatedAt: string;
};

const posts: [Posts] = [
  {
    slug: "my-new-post",
    title: "My New Post",
    excerpt: "Post excerpt",
    updatedAt: "March, 10",
  },
];

describe("Posts page", () => {
  let getPrismicClientMocked: jest.MockedFunction<typeof getPrismicClient>;

  beforeEach(() => {
    getPrismicClientMocked = mockFunction(getPrismicClient);
  });

  it("renders correctly", () => {
    render(Posts({ posts }));

    expect(screen.getByText("My New Post")).toBeInTheDocument();
  });

  it("loads initial data", async () => {
    getPrismicClientMocked.mockReturnValueOnce({
      query: jest.fn().mockResolvedValueOnce({
        results: [
          {
            uid: "my-new-post",
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
                  text: "Post excerpt",
                },
              ],
            },
            last_publication_date: "04-01-2021",
          },
        ],
      }),
    } as any);

    const response = await getStaticProps({});

    expect(response).toEqual(
      expect.objectContaining({
        props: {
          posts: [
            {
              slug: "my-new-post",
              title: "My new post",
              excerpt: "Post excerpt",
              updatedAt: "01 de abril de 2021",
            },
          ],
        },
      })
    );
  });
});
