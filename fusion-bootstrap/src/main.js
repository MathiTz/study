import { HttpLink } from '@apollo/client';
import {
  ApolloClient,
  InMemoryCache,
  ApolloProvider,
  gql,
} from '@apollo/client';
import fetch from 'isomorphic-fetch';
import App from 'fusion-react';
import Root from './components/root';

import HelmetPlugin from 'fusion-plugin-react-helmet-async';

const client = new ApolloClient({
  link: new HttpLink({
    uri: 'https://flyby-router-demo.herokuapp.com/',
    fetch,
  }),
  cache: new InMemoryCache(),
});

const ApolloContext = () => (
  <ApolloProvider client={client}>
    <Root />
  </ApolloProvider>
);

export default async function start() {
  const app = new App(<ApolloContext />);
  app.register(HelmetPlugin);
  return app;
}
