import { fetchWithCache } from '@/app/api/fetch';
import { RouteModel } from '../../utils/models';
import { MapDriver } from './MapDriver';

export async function getRoutes() {
  const response = await fetchWithCache('http://localhost:3000/routes', {
    cache: 'force-cache',
    next: {
      tags: ['routes'],
    },
  });
  //revalidate por demanda
  return response.json();
}

export async function getRoute(route_id: string): Promise<RouteModel> {
  const response = await fetchWithCache(
    `http://localhost:3000/routes/${route_id}`,
    {
      cache: 'force-cache',
      next: {
        tags: [`routes-${route_id}`, 'routes'],
      },
    }
  );

  return response.json();
}

export async function DriverPage({
  searchParams,
}: {
  searchParams: Promise<{ route_id: string }>;
}) {
  const routes = await getRoutes();
  const { route_id } = await searchParams;
  let start_location,
    end_location = null;
  if (route_id) {
    const route = await getRoute(route_id);
    const leg = route.directions.routes[0].legs[0];

    start_location = {
      lat: leg.start_location.lat,
      lng: leg.start_location.lng,
    };
    end_location = {
      lat: leg.end_location.lat,
      lng: leg.end_location.lng,
    };
  }

  return (
    <div className="grid grid-cols-map h-[40rem]">
      <div className="w-full p-4 h-full flex flex-col">
        <h4 className="text-3xl text-contrast mb-2">Inicie uma rota</h4>
        <div className="flex flex-col">
          <form method="get" className="flex flex-col space-y-4">
            <select
              name="route_id"
              className="mb-2 p-2 border rounded bg-default text-contrast"
            >
              {routes.map((route: RouteModel) => (
                <option key={route.id} value={route.id}>
                  {route.name}
                </option>
              ))}
            </select>
            <button
              type="submit"
              className="bg-main text-primary p-2 rounded text-xl font-bold"
              style={{ width: '100%' }}
            >
              Iniciar a viagem
            </button>
          </form>
        </div>
      </div>
      <MapDriver
        route_id={route_id}
        start_location={start_location!}
        end_location={end_location!}
      />
    </div>
  );
}

export default DriverPage;
