// This file controls the visualization
// TEMPLATE
import { loadData } from "./load_data.js";

let input, output;

let overlay = document.getElementById("overlay");
overlay.style.display = "block";

await loadData().then((data) => {
  if (!data) {
    document.getElementById("text").innerText = "Could not load output data";
    throw new Error("Could not load output data");
  } else {
    overlay.style.display = "none";
    input = data.input;
    output = data.output;
  }
});
