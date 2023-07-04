import { List, ListRowRenderer } from "react-virtualized";
import { ProductItem } from "./ProductItem";

interface SearchResultsProps {
  results: Array<{
    id: number;
    price: number;
    priceFormatted: string;
    title: string;
  }>;
  onAddToWishlist: (id: number) => void;
  totalPrice: number;
}

export function SearchResults({
  results,
  onAddToWishlist,
  totalPrice,
}: SearchResultsProps) {
  const rowRenderer: ListRowRenderer = ({ index, key, style }) => {
    return (
      <div key={key} style={style}>
        <ProductItem
          onAddToWishlist={onAddToWishlist}
          product={results[index]}
        />
      </div>
    );
  };

  return (
    <div>
      <h2>{totalPrice}</h2>

      <List
        height={300}
        rowHeight={30}
        width={900}
        overscanRowCount={5}
        rowCount={results.length}
        rowRenderer={rowRenderer}
      />

      {/* {results.map((product, index) => {
        return (
          <ProductItem
            onAddToWishlist={onAddToWishlist}
            key={index}
            product={product}
          />
        );
      })} */}
    </div>
  );
}

/**
 * 1. Criar uma nova versão do component
 * 2. Comparar com a versão anterior
 * 3. Se houverem alterações, vai atualizaar o que alterou
 */

/**
 * 1. Pure Functional Components
 * 2. Renders too often
 * 3. Re-renders with same props
 * 4. Medium to big size
 */

/**
 * useMemo / useCallback
 *
 * 1. Cálculos pesados
 * 2. Igualdade referencial(quando a gente repassa aquela informação a um component filho)
 */
