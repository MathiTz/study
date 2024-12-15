# Pai para filho

```tsx
<Pai>
  <Filho />
</Pai>
```

# Propriedade

```tsx
<Pai>
  <Filho title="" />
</Pai>
```

# Hooks (useState, useContext, useReducer)

```tsx
function Component() {
  const [estado, setEstado] = useState();
}
```

# Fluxo de renderização

1. Gerar uma nova versão do component que precisa ser renderizado;
2. Comparar essa nova versão com a versão anterior já sava na página;
3. Se houverem alterações, o React "renderiza" essa nova versão em tela;
