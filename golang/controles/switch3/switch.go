package main

import "time"

func tipo(i interface{}) string {
	switch i.(type) {
	case int:
		return "inteiro"
	case float32, float64:
		return "real"
	case string:
		return "string"
	case func():
		return "função"
	default:
		return "tipo não esperado"
	}
}

func main() {
	println(tipo(2.3))
	println(tipo(1))
	println(tipo("Opa"))
	println(tipo(func() {}))
	println(tipo(time.Now()))
}
