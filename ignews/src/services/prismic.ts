import * as prismic from '@prismicio/client'

export const repositoryName = 'ignews-matt';
const endpoint = prismic.getRepositoryEndpoint(repositoryName);

export function getPrismicClient() {
    const client = prismic.createClient(endpoint, {
        accessToken: process.env.PRISMIC_ACCESS_TOKEN,
    });

    return client
}
