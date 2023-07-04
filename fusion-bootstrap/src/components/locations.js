import { useQuery } from '@apollo/client';
import { GET_LOCATIONS } from '../queries';

export function DisplayLocations() {
  const { loading, error, data } = useQuery(GET_LOCATIONS);

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error :{error.message}</p>;

  return data.locations.map(({ id, name, description, photo }) => (
    <div key={id}>
      <h2>{name}</h2>
      <img src={photo} alt="location-reference" width={400} height={250} />
      <br />
      <b>About this location</b>
      <p>{description}</p>
      <br />
    </div>
  ));
}
