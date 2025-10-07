// DIRECT core-c loader — no PHP API calls needed

// Memory Allocation (direct read from files/core-c)

export async function loadData() {
  // Build base path to core-c store (adjust if your web root differs)
  const base = `../../../files/core-c/m-${mid}`;

  // Fetch text files directly
  const [inputText, outputText] = await Promise.all([
    fetch(`${base}/in-${mid}.dat`,  { cache: "no-store" }).then(r => r.ok ? r.text() : Promise.reject(new Error(`Missing in-${mid}.dat`))),
    fetch(`${base}/out-${mid}.dat`, { cache: "no-store" }).then(r => r.ok ? r.text() : Promise.reject(new Error(`Missing out-${mid}.dat`))),
  ]);

  // Parse the texts using the same logic as before
  return {
    input:  parseInputDataFile(inputText),
    output: parseOutputDataFile(outputText),
  };
}

function parseInputDataFile(text) {
  let lines = text.split('\n');

  // memory slots
  let numMemSlots = parseInt(lines[0]);
  let memSlots = [];
  for (let i = 1; i <= numMemSlots; i++) {
    let memSlot = lines[i].trim().split(' ').map(Number);
    if (memSlot.length === 2 && !Number.isNaN(memSlot[0]) && !Number.isNaN(memSlot[1])) {
      memSlots.push({ start: memSlot[0], end: memSlot[1] });
    }
  }

  // processes
  let pIndex = numMemSlots + 1;
  let numProcesses = parseInt(lines[pIndex]);
  let processes = [];
  for (let i = pIndex + 1; i <= (pIndex + numProcesses); i++) {
    let process = lines[i]?.trim().split(' ').map(Number) || [];
    if (process.length === 2 && !Number.isNaN(process[0]) && !Number.isNaN(process[1])) {
      processes.push({ id: process[0], size: process[1] });
    }
  }

  return { memSlots, processes };
}

function parseOutputDataFile(text) {
  let processSlots = [];
  text.split('\n').forEach((line) => {
    let arr = line.trim().split(' ').map(Number);
    if (arr.length === 3 && arr.every(n => !Number.isNaN(n))) {
      processSlots.push({ start: arr[0], end: arr[1], id: arr[2] });
    }
  });
  return processSlots;
}
