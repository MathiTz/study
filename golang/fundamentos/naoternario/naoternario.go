package main

// Não tem operador ternário em Go
func obterResultado(nota float64) string {
	if nota >= 6 {
		return "Aprovado"
	}
	return "Reprovado"
}

func main() {
	println(obterResultado(6.2))
}
