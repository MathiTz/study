export function fetchWithCache(url: string, options?: RequestInit) {
  return fetch(url, {
    ...options,
    // cache: 'force-cache',
    // next: {
    //   revalidate: 1 * 60 * 60 * 24, // 1 dia
    // },
  });
}
