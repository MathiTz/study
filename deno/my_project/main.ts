Deno.serve({ port: 8080 }, async (req) => {
  const url = new URL(req.url);

  if (url.pathname === '/webhook') {
    const body = await req.text();
    console.log(body);
    return new Response('Webhook received', { status: 200 });
  }
  return new Response('Not Found', { status: 404 });
});
