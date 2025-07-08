package main

import (
	"fmt"
	"log"
	"sort"
	"strconv"

	"github.com/xuri/excelize/v2"
)

func main() {
	// Abrir o arquivo Excel
	f, err := excelize.OpenFile("ciclo.xlsx")
	if err != nil {
		log.Fatalf("Erro ao abrir arquivo: %v", err)
	}
	defer func() {
		if err := f.Close(); err != nil {
			log.Println("Erro ao fechar arquivo:", err)
		}
	}()

	// Obter todas as planilhas
	sheets := f.GetSheetList()
	if len(sheets) == 0 {
		log.Fatal("O arquivo não contém planilhas")
	}

	// Usar a primeira planilha por padrão
	sheetName := sheets[0]

	// Ler todas as linhas da planilha
	rows, err := f.GetRows(sheetName)
	if err != nil {
		log.Fatalf("Erro ao ler linhas: %v", err)
	}

	// Como sabemos que os índices da Região e UF são 0 e 1 respectivamente
	// e o índice de vagas é 15
	regiaoIdx := 0
	ufIdx := 1
	vagasIdx := 15

	// Verificar se temos cabeçalho
	if len(rows) == 0 {
		log.Fatal("O arquivo está vazio")
	}

	header := rows[0]

	// Armazenar linhas filtradas
	var filteredRows [][]string
	filteredRows = append(filteredRows, header) // Adicionar cabeçalho

	// Filtrar linhas
	for i, row := range rows {
		// Pular cabeçalho
		if i == 0 {
			continue
		}

		// Verificar se a linha tem células suficientes
		if len(row) > vagasIdx {
			regiao := row[regiaoIdx]
			uf := row[ufIdx]

			// Filtrar por Nordeste e CE
			if regiao == "Nordeste" && uf == "CE" {
				filteredRows = append(filteredRows, row)
			}
		}
	}

	// Ordenar por número de vagas (índice 15)
	sort.Slice(filteredRows[1:], func(i, j int) bool {
		// Ajustando índices para considerar o cabeçalho
		row1 := filteredRows[i+1]
		row2 := filteredRows[j+1]

		// Converter strings para números
		vagas1, err1 := strconv.Atoi(row1[vagasIdx])
		if err1 != nil {
			vagas1 = 0 // Tratar erro como 0 vagas
		}

		vagas2, err2 := strconv.Atoi(row2[vagasIdx])
		if err2 != nil {
			vagas2 = 0 // Tratar erro como 0 vagas
		}

		return vagas1 > vagas2 // Ordem decrescente
	})

	// Imprimir resultados no console
	fmt.Println("Dados filtrados (Região Nordeste e UF CE) ordenados por número de vagas:")
	fmt.Println("----------------------------------------------------------------------")
	for _, row := range filteredRows {
		fmt.Println(formatRow(row))
	}

	// Criar novo arquivo Excel
	newFile := excelize.NewFile()
	defer func() {
		if err := newFile.Close(); err != nil {
			log.Println("Erro ao fechar novo arquivo:", err)
		}
	}()

	// Definir nome da planilha
	newSheetName := "Nordeste-CE"
	newFile.SetSheetName("Sheet1", newSheetName)

	// Adicionar dados ao novo arquivo
	for i, row := range filteredRows {
		for j, cell := range row {
			cellName, err := excelize.CoordinatesToCellName(j+1, i+1)
			if err != nil {
				log.Printf("Erro ao converter coordenadas: %v", err)
				continue
			}
			err = newFile.SetCellValue(newSheetName, cellName, cell)
			if err != nil {
				log.Printf("Erro ao definir valor da célula: %v", err)
			}
		}
	}

	// Salvar novo arquivo
	outputFile := "nordeste_ce_ordenado.xlsx"
	if err := newFile.SaveAs(outputFile); err != nil {
		log.Fatalf("Erro ao salvar arquivo: %v", err)
	}

	fmt.Printf("\nDados filtrados foram salvos em: %s\n", outputFile)
}

// formatRow formata uma linha para impressão
func formatRow(row []string) string {
	result := ""
	for i, cell := range row {
		if i > 0 {
			result += " | "
		}
		result += cell
	}
	return result
}
