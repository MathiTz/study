'use client';

import { RefObject, useEffect, useRef } from 'react';
import { useMap } from '../../hooks/useMap';
import { socket } from '@/utils/socket-io';

export type MapDriverProps = {
  route_id?: string;
  start_location?: {
    lat: number;
    lng: number;
  };
  end_location?: {
    lat: number;
    lng: number;
  };
};

export function MapDriver({
  route_id,
  end_location,
  start_location,
}: MapDriverProps) {
  const mapContainerRef = useRef<HTMLDivElement>(
    null
  ) as RefObject<HTMLDivElement>;
  const map = useMap(mapContainerRef);

  useEffect(() => {
    if (!map || !route_id || !start_location || !end_location) return;

    if (socket.disconnected) {
      socket.connect();
    } else {
      socket.offAny();
    }

    socket.on('connect', () => {
      console.log('connected');
      socket.emit(`client:new-points`, { route_id });
    });

    socket.on(
      `server:new-points/${route_id}:list`,
      (data: { route_id: string; lat: number; lng: number }) => {
        if (!map.hasRoute(data.route_id)) {
          map.addRouteWithIcons({
            routeId: data.route_id,
            startMarkerOptions: {
              position: start_location,
            },
            endMarkerOptions: {
              position: end_location,
            },
            carMarkerOptions: {
              position: start_location,
            },
          });
        }

        map.moveCar(data.route_id, {
          lat: data.lat,
          lng: data.lng,
        });
      }
    );

    return () => {
      socket.disconnect();
    };
  }, [route_id, map, start_location, end_location]);

  return <div className="w-full h-[39rem]" ref={mapContainerRef} />;
}
