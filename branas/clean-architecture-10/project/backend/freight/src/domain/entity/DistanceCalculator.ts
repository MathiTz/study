import Coord from './Coord';

export default class DistanceCalculator {
  static calculate(from: Coord, to: Coord) {
    if (from.lat === to.lat && from.long === to.long) return 0;
    const radlat1 = (Math.PI * from.lat) / 180;
    const radlat2 = (Math.PI * to.lat) / 180;
    const theta = from.long - to.long;
    const radtheta = (Math.PI * theta) / 180;
    let dist =
      Math.sin(radlat1) * Math.sin(radlat2) +
      Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
    if (dist > 1) dist = 1;
    dist = Math.acos(dist);
    dist = (dist * 180) / Math.PI;
    return dist * 60 * 1.1515 * 1.609344;

    // Mais preciso
    // const R = 6371e3; // metres
    // const φ1 = (from.lat * Math.PI) / 180; // φ, λ in radians
    // const φ2 = (to.lat * Math.PI) / 180;
    // const Δφ = ((to.lat - from.lat) * Math.PI) / 180;
    // const Δλ = ((to.long - from.long) * Math.PI) / 180;

    // const a =
    //   Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    //   Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    // const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    // return R * c * 0.001; // in kilometers
  }
}
