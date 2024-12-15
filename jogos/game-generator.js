const fs = require('fs');

// Caminho para o arquivo JSON
const caminhoArquivo = './jogos.json';

// Função para ler o arquivo JSON e obter o array resultadosPrevios
/**
 *
 * @param {string} caminhoArquivo
 * @returns {Array<Array<number>>} - Array com os resultados prévios
 */
function lerArquivoJSON(caminhoArquivo) {
  const conteudoArquivo = fs.readFileSync(caminhoArquivo, 'utf-8');
  const json = JSON.parse(conteudoArquivo);
  const resultadosPrevios = Object.values(json);

  return resultadosPrevios;
}

// Resultados prévios
const resultadosPrevios = lerArquivoJSON(caminhoArquivo);

/**
 * Finds the probable numbers from a given array of previous results
 * @param {Array} resultadosPreviosArray - Array of previous results
 * @returns {Object} - Object with the probable numbers and their frequencies
 */
function encontrarNumerosProvaveis(resultadosPreviosArray) {
  const frequencia = {};
  let numeroTotalJogos = 0;

  for (const resultados of resultadosPreviosArray) {
    for (const numero of resultados) {
      for (const num of numero) {
        frequencia[num] = (frequencia[num] || 0) + 1;
        numeroTotalJogos++;
      }
    }
  }

  frequencia.total = numeroTotalJogos;

  return frequencia;
}

// Encontra a frequência de todos os números
const frequenciaNumeros = encontrarNumerosProvaveis(resultadosPrevios);

function determineProbability(frequenciaNumeros) {
  const totalGames = frequenciaNumeros.total;
  const frequencyWithProbability = {};

  for (const numero in frequenciaNumeros) {
    if (numero !== 'total') {
      frequencyWithProbability[numero] = (
        (frequenciaNumeros[numero] / totalGames) *
        100
      ).toFixed(2);
    }
  }

  return frequencyWithProbability;
}

const frequencyWithProbability = determineProbability(frequenciaNumeros);

/**
 * Generates an array of probable games
 * @param {Object} frequencyWithProbability - Object with the probable numbers and their frequencies
 * @param {number} quantity - Quantity of probable games to be generated
 * @returns {Array<Array<number>>} - Array with the probable games
 */
function generateGameArrays(frequencyWithProbability, quantity) {
  const probableArrays = [];

  const orderedNumbers = Object.keys(frequencyWithProbability)
    .sort((a, b) => frequencyWithProbability[b] - frequencyWithProbability[a])
    .slice(0, Object.keys(frequencyWithProbability).length / 2)
    .map((n) => Number(n))
    .sort();

  for (let i = 0; i < quantity; i++) {
    const array = [];

    for (let i = 0; i < 6; i++) {
      const randomNumber = Math.floor(Math.random() * orderedNumbers.length);

      if (!array.includes(orderedNumbers[randomNumber])) {
        array.push(orderedNumbers[randomNumber]);
      }
    }

    probableArrays.push(array);
  }

  return probableArrays;
}

// const probableGames = generateGameArrays(frequencyWithProbability, 6);

function generateGameWithImprobableNumber(frequencyWithProbability, quantity) {
  const gameArray = [];

  const orderedNumbers = Object.keys(frequencyWithProbability)
    .sort((a, b) => frequencyWithProbability[b] - frequencyWithProbability[a])
    .map((n) => Number(n));

  const improbableNumber = orderedNumbers
    .slice(Object.keys(frequencyWithProbability).length / 2)
    .sort();

  const probableNumbers = orderedNumbers
    .slice(0, Object.keys(frequencyWithProbability).length / 2)
    .sort();

  for (let i = 0; i < quantity; i++) {
    const array = [];
    let probableNumberCount = 0;

    for (let j = 0; j < 6; j++) {
      const randomNumber = Math.floor(
        (Math.random() * orderedNumbers.length) / 2
      );
      if (!array.includes(improbableNumber[randomNumber])) {
        array.push(improbableNumber[randomNumber]);
      } else if (probableNumberCount < i) {
        if (!array.includes(probableNumbers[randomNumber])) {
          array.push(probableNumbers[randomNumber]);
          probableNumberCount++;
        }
      }
    }

    gameArray.push(array);
  }

  return gameArray;
}

console.log(
  generateGameWithImprobableNumber(frequencyWithProbability, 6),
  generateGameArrays(frequencyWithProbability, 6)
);
