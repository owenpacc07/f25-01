//Handles movement of info throughout the page a seperate script will handle THREE.js background

var slider; //tracks slider for other functions
var sliderCollection = new Array();



//Slider object to store values
class SliderDocumentor {
    constructor(location, value) {
        this.location = location;
        this.value = value;
    }
}

//This reads, outputs current slider values, and pumps it into sliderCollection Array
function sliderValuePrinter(targetReadId, targetPrintId){
    slider = document.getElementById(targetReadId);
    var output = document.getElementById(targetPrintId);
    output.innerHTML = slider.value;

    slider.oninput = function() {
        output.innerHTML = this.value;
        let addIn = new SliderDocumentor(targetReadId,slider.value);
        sliderCollection.push(addIn);
    }
}


// function tableManager(){
//     let btnAdd = document.getElementById('button-data-add');
//     let table = document.getElementById('table-data');
    
//     let inputDomain = document.querySelector(".input-domain");
//     let inputObject = document.querySelector(".input-objects");
//     let inputFunction = document.querySelector(".input-functions");

//     btnAdd.addEventListener('click',() => {
//         let domain = inputDomain.value;
//         let object = inputObject.value;
//         let functionVar = inputFunction.value;

//         let template = `
//                         <tr>
//                             <td>${domain}</td>
//                             <td>${object}</td>
//                             <td>${functionVar}</td>
//                         </tr>
//                         `;
        
//         table.innerHTML += template;
//     });
// }


/*
Domain is a user group.
- Hypervisor
- Kernel  
- Admin
- User
- Process
- Guest

*/


var btnAdd = document.querySelector(".button-data-add");
var table = document.querySelector('.table-data');

var inputDomain = document.querySelector('.input-domain');
var inputObject = document.querySelector('.input-objects');
var inputFunction = document.querySelector('.input-functions');
console.log(btnAdd.value);

btnAdd.addEventListener('click', () => {
    // console.log("Button was clicked");
    let domain = inputDomain.value;
    let object = inputObject.value;
    let functionVar = inputFunction.value;

    let template = `
                    <tr>
                        <td>${domain}</td>
                        <td>${object}</td>
                        <td>${functionVar}</td>
                        <td><button>Delete</button><td>
                    </tr>
                    `;
    
    table.innerHTML += template;
});
