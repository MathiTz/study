package bar

import "avancado/foo"

func TakeFoo(i foo.Interface) {
	i.Interface()
}
