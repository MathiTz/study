package main

import "fmt"

type esportivo interface {
	ligarTurbo()
}

type ferrari struct {
	modelo          string
	turboLigado     bool
	velocidadeAtual int
}

func (f *ferrari) ligarTurbo() {
	f.turboLigado = true
}

func main() {
	f40 := ferrari{"F40", false, 0}
	f40.ligarTurbo()

	var f1 esportivo = &ferrari{"F40", false, 0}
	f1.ligarTurbo()

	fmt.Println(f40, f1)
}
