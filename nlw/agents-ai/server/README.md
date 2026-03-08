# NLW Agents

Projeto desenvolvido durante o evento **NLW Agents** da Rocketseat, focado na construção de uma API robusta para gerenciamento de agentes de IA.

## 🚀 Tecnologias

- **Node.js** - Runtime JavaScript
- **TypeScript** - Superset tipado do JavaScript
- **Fastify** - Framework web de alta performance
- **Drizzle ORM** - ORM TypeScript-first para PostgreSQL
- **PostgreSQL** - Banco de dados relacional
- **Docker** - Containerização da aplicação
- **Zod** - Validação de esquemas TypeScript
- **Biome** - Linter e formatter

## 🏗️ Arquitetura

- **Framework**: Fastify com TypeScript nativo
- **Validação**: Zod para type-safe schemas
- **Banco de dados**: PostgreSQL com Drizzle ORM
- **Padrões**: API REST com validação de tipos
- **Estrutura**: Organização modular com separação de responsabilidades

## ⚙️ Configuração

### Pré-requisitos

- Node.js (versão 20 ou superior)
- Docker e Docker Compose

### 1. Clone o repositório

```bash
git clone <repository-url>
cd server
```

### 2. Configure as variáveis de ambiente

Crie um arquivo `.env` na raiz do projeto:

```env
PORT=3333
DATABASE_URL=postgres://docker:docker@localhost:5432/agents
```

### 3. Inicie o banco de dados

```bash
docker-compose up -d
```

### 4. Execute as migrações

```bash
npx drizzle-kit migrate
```

### 5. (Opcional) Execute o seed do banco de dados

```bash
npm run db:seed
```

### 6. Inicie o servidor

```bash
# Desenvolvimento
npm run dev

# Produção
npm start
```

## 📝 Scripts Disponíveis

- `npm run dev` - Inicia o servidor em modo desenvolvimento com hot reload
- `npm start` - Inicia o servidor em modo produção
- `npm run db:seed` - Executa o seed do banco de dados

## 🔗 Endpoints

- `GET /health` - Verificação de saúde da API
- `GET /rooms` - Listagem de salas
- `POST /rooms` - Criação de uma nova sala
- `GET /rooms/:roomId/questions` - Listar perguntas de uma sala
- `POST /rooms/:roomId/questions` - Criar pergunta em uma sala

## 📄 Exemplo de uso com client.http

O arquivo `client.http` pode ser usado para testar rapidamente a API. Exemplo de requisições:

```http
# Health check
GET http://localhost:3333/health

###

# Listar salas
GET http://localhost:3333/rooms

###

# Criar sala
POST http://localhost:3333/rooms
Content-Type: application/json

{
  "name": "New Room",
  "description": "A description of the new room"
}

###

# Listar perguntas de uma sala (substitua {{roomId}} pelo ID retornado na criação da sala)
GET http://localhost:3333/rooms/{{roomId}}/questions

###

# Criar pergunta em uma sala
POST http://localhost:3333/rooms/{{roomId}}/questions
Content-Type: application/json

{
  "question": "Qual o conceito de X dentro de Y?"
}
```

## 🐳 Docker

O projeto utiliza PostgreSQL com extensão pgvector através do Docker Compose para facilitar o desenvolvimento local.

---

_Desenvolvido durante o NLW Agents da Rocketseat_
