# Preparação para Entrevista de Design de Sistemas

## Áreas para Melhorar

### Balanceamento de Carga & Edge Computing

#### Balanceamento de Carga
**O que é**: Balanceamento de carga é a técnica de distribuir o tráfego de rede ou carga de processamento entre vários servidores para garantir que nenhum servidor fique sobrecarregado. Isso ajuda a melhorar a disponibilidade, confiabilidade e escalabilidade de uma aplicação.

**Tradeoffs**:
- **Complexidade**: Implementar e gerenciar balanceadores de carga pode adicionar complexidade ao sistema.
- **Custo**: Pode aumentar os custos operacionais devido à necessidade de hardware ou serviços adicionais.
- **Latência**: Pode introduzir latência adicional, especialmente se o balanceador de carga estiver geograficamente distante dos servidores.

#### Edge Computing
**O que é**: Edge computing é a prática de processar dados perto da borda da rede, onde os dados são gerados, em vez de em um data center centralizado. Isso reduz a latência e a largura de banda necessária, melhorando a performance para os usuários finais.

**Tradeoffs**:
- **Segurança**: Processar dados na borda pode introduzir novos vetores de ataque e desafios de segurança.
- **Gerenciamento**: Requer gerenciamento de infraestrutura distribuída, o que pode ser mais complexo do que gerenciar um data center centralizado.
- **Custo**: Pode ser mais caro devido à necessidade de hardware adicional e manutenção em múltiplas localizações.

- **Conceitos de Balanceamento de Carga Geográfico Mal Interpretados**: Entenda equívocos comuns e práticas corretas na distribuição de tráfego com base na localização geográfica.
- **Revisar Lambda@Edge e Funções do CloudFront**: Aprenda como executar código mais próximo dos usuários para reduzir a latência usando serviços da AWS.
- **Estudar Soluções a Nível de DNS**:
  - **GeoDNS**: Direciona usuários para diferentes servidores com base na localização geográfica.
  - **Anycast**: Roteia o tráfego para o servidor mais próximo ou com melhor desempenho usando o mesmo endereço IP.

### Dados Geoespaciais
- **Revisar Tradeoffs entre PostGIS e MongoDB**:
  - **Requisitos ACID**: Garantir a integridade dos dados com transações.
  - **Padrões de Leitura/Escrita**: Otimizar para padrões específicos de acesso aos dados.
  - **Necessidades de Estrutura de Dados**: Escolher o banco de dados certo com base na complexidade dos dados.
  - **Capacidades em Tempo Real**: PostGIS oferece consultas geoespaciais avançadas e conformidade ACID, tornando-o ideal para operações complexas. MongoDB com GeoJSON fornece flexibilidade e escalabilidade horizontal, adequado para necessidades geoespaciais mais simples.
- **Entender Estratégias de Sharding por Localização**: Distribuir dados em vários servidores com base na localização geográfica.
- **Aprofundar Conhecimento em Soluções de Cache**:
  - **Implementação de GEOHASH no Redis**: Indexar e consultar dados geoespaciais de forma eficiente.
  - **Consultas Geoespaciais no Elasticsearch**: Realizar buscas geoespaciais complexas.
  - **Otimização de Índices**: Melhorar a performance das consultas com índices otimizados.
  - **ElasticSearch vs Redis GEOHASH**:
    - **Consultas Geoespaciais no ElasticSearch**:
      - **Vantagens**:
        - **Capacidades Avançadas de Consulta**: Suporta consultas geoespaciais complexas, incluindo cálculos de distância, caixas delimitadoras e buscas por polígonos.
        - **Escalabilidade**: Projetado para lidar com grandes volumes de dados e escalar horizontalmente.
        - **Integração**: Integra-se bem com outros componentes do Elastic Stack para análises e visualização.
      - **Desvantagens**:
        - **Complexidade**: Mais complexo de configurar e gerenciar em comparação com o Redis.
        - **Performance**: Pode ter maior latência para consultas geoespaciais simples em comparação com o Redis.
    - **Redis GEOHASH**:
      - **Vantagens**:
        - **Performance**: Extremamente rápido para consultas geoespaciais simples, como buscas por raio e vizinhos mais próximos.
        - **Simplicidade**: Fácil de configurar e usar com configuração mínima.
        - **Em Memória**: Opera inteiramente em memória, proporcionando acesso de baixa latência.
      - **Desvantagens**:
        - **Capacidades Limitadas de Consulta**: Suporta menos tipos de consultas geoespaciais em comparação com o ElasticSearch.
        - **Uso de Memória**: Pode consumir uma quantidade significativa de memória para grandes conjuntos de dados.

### Análise & Visualização
- **Estudar Análises Comuns em Logística**:
  - **Mapas de Calor Baseados no Tempo**: Visualizar dados ao longo do tempo para identificar tendências.
  - **Mapeamento de Densidade**: Mostrar a concentração de dados em áreas específicas.
  - **Métricas de Performance**: Medir e analisar a performance do sistema.

### Arquitetura
- **Fortalecer Conhecimento em Sistemas Distribuídos**:
  - **Padrões de Comunicação de Serviços (REST/GraphQL/Filas)**: Escolher o método de comunicação certo para seus serviços.
  - **Abordagens de Escalabilidade**: Projetar sistemas que possam lidar com aumento de carga.
  - **Considerações de Sobrecarga de Rede**: Minimizar latência e uso de largura de banda.

### Tolerância a Falhas
- **Revisar**:
  - **Tratamento de Falhas Parciais**: Projetar sistemas para lidar graciosamente com falhas.
  - **Padrão de Circuit Breaker**: Prevenir falhas em cascata parando chamadas para serviços com falhas.
  - **Estratégias de Backup**: Garantir a disponibilidade e recuperação dos dados.

### Processamento em Tempo Real
- **Estudar**:
  - **Mecanismos de Ordenação de Eventos**: Garantir que eventos sejam processados na ordem correta.
  - **Processamento Exatamente Uma Vez**: Garantir que cada evento seja processado apenas uma vez.
  - **Gerenciamento de Estado**: Manter e gerenciar o estado em aplicações em tempo real.

### Observabilidade & Segurança
- **Pesquisar**:
  - **Implementação de Rastreamento Distribuído**: Rastrear requisições através de múltiplos serviços para diagnosticar problemas.
  - **Ferramentas Comuns de APM**: Monitorar e gerenciar a performance da aplicação.
    - **New Relic**
    - **Datadog**
    - **AppDynamics**
    - **Dynatrace**
    - **Prometheus**
    - **Grafana**
  - **Padrões de Autenticação**: Autenticar usuários e serviços de forma segura.
  - **Estratégias de Limitação de Taxa**: Controlar a taxa de requisições para prevenir abusos e garantir estabilidade.

### Dados Estruturados vs. Dados Não Estruturados

#### Dados Estruturados
**O que é**: Dados estruturados são dados organizados em um formato definido, como tabelas em bancos de dados relacionais, onde cada campo tem um tipo de dado específico (e.g., números, datas, strings).

**Características**:
- **Formato**: Estruturado e organizado.
- **Armazenamento**: Bancos de dados relacionais (e.g., SQL).
- **Consultas**: Fácil de consultar usando linguagens como SQL.
- **Exemplos**: Dados de transações financeiras, registros de clientes.

#### Dados Não Estruturados
**O que é**: Dados não estruturados são dados que não têm um formato ou estrutura predefinida. Eles podem incluir texto, imagens, vídeos, e-mails, documentos, etc.

**Características**:
- **Formato**: Não estruturado e desorganizado.
- **Armazenamento**: Bancos de dados NoSQL, sistemas de arquivos.
- **Consultas**: Mais difícil de consultar e analisar.
- **Exemplos**: E-mails, documentos de texto, vídeos, imagens.

### Websockets vs gRPC vs GraphQL vs HTTP em Aplicações em Tempo Real

#### Websockets
**Vantagens**:
- **Baixa Latência**: Proporciona comunicação full-duplex de baixa latência entre cliente e servidor.
- **Atualizações em Tempo Real**: Ideal para aplicações que requerem atualizações em tempo real (e.g., aplicações de chat, notificações ao vivo).
- **Eficiente**: Reduz a sobrecarga mantendo uma conexão persistente.

**Desvantagens**:
- **Complexidade**: Mais complexo de implementar e gerenciar em comparação com HTTP.
- **Escalabilidade**: Requer manuseio cuidadoso das conexões para escalar efetivamente.
- **Compatibilidade**: Nem todos os ambientes suportam Websockets nativamente.

#### gRPC
**Vantagens**:
- **Performance**: Alta performance com baixa latência e serialização binária eficiente (Protocol Buffers).
- **Streaming**: Suporta streaming bidirecional, tornando-o adequado para aplicações em tempo real.
- **Tipagem Forte**: Impõe tipagem forte e design de API baseado em contrato.

**Desvantagens**:
- **Complexidade**: Mais complexo de configurar e requer aprendizado de Protocol Buffers.
- **Suporte a Navegadores**: Suporte limitado a navegadores em comparação com HTTP/GraphQL.
- **Ferramentas**: Ecossistema de ferramentas menos maduro em comparação com REST/HTTP.

#### GraphQL
**Vantagens**:
- **Consultas Flexíveis**: Clientes podem solicitar exatamente os dados de que precisam, reduzindo o excesso de dados.
- **Capacidades em Tempo Real**: Suporta atualizações em tempo real através de subscriptions.
- **Endpoint Único**: Simplifica o design da API usando um único endpoint para todas as consultas.

**Desvantagens**:
- **Complexidade**: Mais complexo de implementar e otimizar em comparação com REST/HTTP.
- **Performance**: Pode introduzir sobrecarga de performance devido à execução dinâmica de consultas.
- **Cache**: Mais desafiador de implementar cache em comparação com REST/HTTP.

#### HTTP (REST)
**Vantagens**:
- **Simplicidade**: Simples de implementar e amplamente compreendido.
- **Ferramentas**: Extensa disponibilidade de ferramentas e bibliotecas.
- **Cache**: Fácil de implementar mecanismos de cache.

**Desvantagens**:
- **Sobrecarga**: Maior sobrecarga devido à natureza stateless e configuração/desconexão repetida de conexões.
- **Latência**: Maior latência em comparação com Websockets e gRPC para atualizações em tempo real.
- **Suporte Limitado a Tempo Real**: Não é inerentemente projetado para comunicação em tempo real.

## Exemplo de Design de Sistema: Otimização de Rotas em Tempo Real para Motoristas de Entrega

### Requisitos
1. Atualizações de rota em tempo real com base no tráfego e status de entrega.
2. Suporte para operação offline quando os motoristas perderem a conectividade.
3. Consultas geoespaciais eficientes para encontrar motoristas próximos e otimizar rotas.
4. Integração com aplicativos móveis para motoristas e painel web separado para a equipe de operações.
5. Performance em escala (milhares de motoristas simultâneos).

### Serviços AWS Recomendados
- **Amazon Kinesis**: Para streaming e processamento de dados em tempo real.
- **AWS Lambda**: Para processar fluxos de dados e acionar a lógica de otimização de rotas.
- **Amazon API Gateway**: Para expor APIs para aplicativos móveis e painéis web.
- **AWS AppSync**: Para sincronização de dados em tempo real e capacidades offline.
- **Amazon DynamoDB**: Para armazenar dados de motoristas e entregas com capacidades de sincronização offline.
- **Amazon Location Service**: Para consultas geoespaciais e otimização de rotas.
- **Amazon RDS (PostGIS)**: Para consultas geoespaciais avançadas e armazenamento de dados.
- **Amazon ElastiCache (Redis)**: Para cache de dados geoespaciais.
- **AWS Amplify**: Para construir e implantar aplicativos móveis e web.
- **Amazon Cognito**: Para autenticação e autorização de usuários.
- **Amazon ECS**: Para executar aplicações containerizadas em escala.
- **Amazon CloudFront**: Para entrega de conteúdo e redução de latência.
- **Amazon SQS**: Para desacoplar e escalar tarefas de processamento.

### Desafios Potenciais
- **Processamento de Dados em Tempo Real**: Garantir baixa latência e escalabilidade.
- **Operação Offline**: Sincronização de dados e gerenciamento de armazenamento local.
- **Consultas Geoespaciais**: Performance e precisão.
- **Integração**: Experiência do usuário e segurança.
- **Performance em Escala**: Balanceamento de carga e gerenciamento de recursos.
- **Otimização de Rotas**: Complexidade do algoritmo e condições dinâmicas.
- **Monitoramento e Observabilidade**: Visibilidade e alertas.
- **Gestão de Custos**: Otimização do custo dos serviços AWS.

### Melhores Práticas para Consistência de Dados
- Usar bancos de dados compatíveis com ACID.
- Implementar transações distribuídas.
- Empregar event sourcing.
- Usar operações idempotentes.
- Implementar estratégias de resolução de conflitos.
- Utilizar hashing consistente.
- Aplicar validação e verificações de integridade dos dados.
- Usar modelos de consistência forte.
- Implementar monitoramento e alertas em tempo real.
- Garantir particionamento e sharding adequados dos dados.

### Melhores Práticas para Implementação de Balanceadores de Carga
1. **Verificações de Saúde**: Monitorar regularmente a saúde dos servidores backend para garantir que o tráfego seja roteado apenas para instâncias saudáveis.
2. **Terminação SSL**: Descarregar o processamento SSL/TLS para o balanceador de carga para reduzir a carga nos servidores backend.
3. **Persistência de Sessão**: Usar sessões persistentes se sua aplicação requer que o mesmo usuário seja direcionado para o mesmo servidor.
4. **Auto-Scaling**: Integrar com grupos de auto-escalonamento para ajustar dinamicamente o número de servidores backend com base no tráfego.
5. **Redundância**: Implantar balanceadores de carga em múltiplas zonas de disponibilidade para garantir alta disponibilidade.
6. **Registro e Monitoramento**: Habilitar registro e monitoramento para rastrear a performance e solucionar problemas.
7. **Segurança**: Implementar medidas de segurança como proteção contra DDoS, lista branca de IPs e limitação de taxa.

### Melhores Práticas para Implementação de API Gateways
1. **Autenticação e Autorização**: Usar gateways de API para impor políticas de autenticação e autorização.
2. **Limitação de Taxa**: Implementar limitação de taxa para prevenir abusos e garantir uso justo das APIs.
3. **Cache**: Usar cache para reduzir a latência e melhorar a performance para recursos acessados frequentemente.
4. **Transformação de Requisições e Respostas**: Transformar requisições e respostas conforme necessário para garantir compatibilidade entre clientes e serviços backend.
5. **Versionamento**: Gerenciar versões de API para garantir compatibilidade retroativa e transições suaves entre versões.
6. **Monitoramento e Análise**: Coletar métricas e registros para monitorar o uso e a performance da API.
7. **Segurança**: Implementar recursos de segurança como SSL/TLS, lista branca de IPs e criptografia de dados.
8. **Documentação**: Fornecer documentação abrangente da API para ajudar os desenvolvedores a entender e usar suas APIs de forma eficaz.
