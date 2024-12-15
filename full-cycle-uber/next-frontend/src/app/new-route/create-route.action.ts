'use server';
import { fetchWithCache } from '@/app/api/fetch';
import { revalidateTag } from 'next/cache';

export async function createRouteAction(
  state: { error?: string; success?: boolean } | null,
  formData: FormData
) {
  const { sourceId, destinationId } = Object.fromEntries(formData);

  const directionsResponse = await fetchWithCache(
    `http://localhost:3000/directions?originId=${sourceId}&destinationId=${destinationId}`
  );

  if (!directionsResponse.ok) {
    return { error: 'Erro ao buscar direções' };
  }

  const directionsData = await directionsResponse.json();

  const startAddress = directionsData.routes[0].legs[0].start_address;
  const endAddress = directionsData.routes[0].legs[0].end_address;

  const routeResponse = await fetch('http://localhost:3000/routes', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      name: `${startAddress} - ${endAddress}`,
      source_id: directionsData.request.origin.place_id.replace(
        'place_id:',
        ''
      ),
      destination_id: directionsData.request.destination.place_id.replace(
        'place_id:',
        ''
      ),
    }),
  });

  if (!routeResponse.ok) {
    return { error: 'Erro ao criar rota' };
  }

  revalidateTag('routes');

  return { success: true };
}
