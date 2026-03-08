# NLW Agents

Aplicação web desenvolvida durante o evento **NLW (Next Level Week)** da **Rocketseat**, focada na criação de uma plataforma interativa para gerenciamento de salas/rooms.

## 🛠️ Tecnologias Utilizadas

- **React 19** - Biblioteca para interfaces de usuário
- **TypeScript** - Superset JavaScript com tipagem estática
- **Vite** - Build tool e bundler para desenvolvimento rápido
- **TailwindCSS 4** - Framework CSS utility-first para estilização
- **React Router DOM** - Roteamento client-side
- **TanStack Query** - Gerenciamento de estado servidor e cache
- **Radix UI** - Componentes acessíveis headless
- **Lucide React** - Biblioteca de ícones

## 🏗️ Padrões e Arquitetura

- **Component Composition** - Utilizando Radix UI e class-variance-authority
- **Path Mapping** - Alias `@/` configurado para facilitar imports
- **Utility-First CSS** - TailwindCSS para estilização consistente
- **Type Safety** - TypeScript com strict mode habilitado
- **Code Quality** - Biome para linting e formatação

## 📁 Estrutura do Projeto

```
src/
├── app.tsx                # Componente principal com providers
├── main.tsx               # Entry point da aplicação
├── index.css              # Estilos globais
├── components/            # Componentes reutilizáveis
│   ├── create-room-form.tsx
│   ├── question-form.tsx
│   ├── question-item.tsx
│   ├── question-list.tsx
│   ├── room-list.tsx
│   └── ui/                # Componentes de UI (Badge, Button, Card, Form, Input, Label, Textarea)
├── http/                  # Hooks e tipos para requisições HTTP
│   ├── use-create-room.ts
│   ├── use-room.ts
│   └── types/
│       ├── create-room-request.ts
│       ├── create-room-response.ts
│       └── get-rooms-response.ts
├── lib/                   # Utilitários e helpers
│   ├── dayjs.ts
│   └── utils.ts
├── pages/                 # Páginas da aplicação
│   ├── create-room.tsx
│   └── room.tsx
└── vite-env.d.ts          # Declarações de tipos do Vite
```

## 🚀 Setup e Configuração

### Pré-requisitos

- Node.js (versão 18 ou superior)
- npm, yarn ou pnpm

### Instalação

1. Clone o repositório e navegue até a pasta do projeto:

```bash
cd web
```

2. Instale as dependências:

```bash
npm install
# ou
yarn install
# ou
pnpm install
```

### Executando o Projeto

```bash
# Modo desenvolvimento
npm run dev

# Build para produção
npm run build

# Preview da build
npm run preview
```

### Configuração do Ambiente

O projeto utiliza Vite como bundler e está configurado para:

- Hot Module Replacement (HMR)
- TypeScript support
- TailwindCSS com Vite plugin
- Path aliases (`@/` aponta para `src/`)

## 📝 Scripts Disponíveis

- `dev` - Inicia o servidor de desenvolvimento
- `build` - Gera build de produção (TypeScript + Vite)
- `preview` - Visualiza a build de produção localmente

---

Desenvolvido com ❤️ durante o **NLW Agents** da [Rocketseat](https://rocketseat.com.br)
