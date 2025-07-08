import { Header } from "./components/Header";
import { Post } from "./Post";

import styles from './App.module.css'
import { Sidebar } from "./components/Sidebar";

export function App() {
  return (
    <div>
      <Header />

      <div className={styles.wrapper}>
        <Sidebar />

        <main className={styles.main}>
          <Post author="Diego Fernandes" content="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus, sagittis eget, iaculis quis, molestie id, mi. Sed pretium, ligula sollicitudin laoreet." />
          <Post author="Diego Fernandes" content="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus, sagittis eget, iaculis quis, molestie id, mi. Sed pretium, ligula sollicitudin laoreet." />
        </main>
      </div>
    </div>
  );
}
