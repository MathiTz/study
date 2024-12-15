import Link from 'next/link';

import styles from './header.module.scss';

export default function Header(): JSX.Element {
  return (
    <header className={styles.header}>
      <Link href="/">
        <h1>
          <img src="/images/spacetraveling.svg" alt="logo" />
          <span className={styles.title}>spacetraveling</span>
          <span className={styles.endLogo}>.</span>
        </h1>
      </Link>
    </header>
  );
}
