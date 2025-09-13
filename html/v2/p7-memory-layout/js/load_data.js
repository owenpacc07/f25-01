/**
 * Memory Layout - Data Loading Module
 * This file handles loading the data for the memory layout visualization
 */

// Sample data for memory layout visualization
const memoryLayoutData = {
    steps: [
        {
            id: 0,
            title: "Initial State",
            description: "The operating system prepares memory spaces for the program.",
            codeHighlight: null,
            dataHighlight: null,
            heapHighlight: null,
            stackHighlight: null
        },
        {
            id: 1,
            title: "Source Code",
            description: "Java source code (.java) is created and stored on disk.",
            codeHighlight: true,
            dataHighlight: null,
            heapHighlight: null,
            stackHighlight: null
        },
        {
            id: 2,
            title: "Compilation",
            description: "Java compiler (javac) translates source code into bytecode (.class).",
            codeHighlight: true,
            dataHighlight: null,
            heapHighlight: null,
            stackHighlight: null
        },
        {
            id: 3,
            title: "JVM Loading",
            description: "Java Virtual Machine loads the bytecode into memory.",
            codeHighlight: true,
            dataHighlight: null,
            heapHighlight: null,
            stackHighlight: null
        },
        {
            id: 4,
            title: "Static Data Allocation",
            description: "Memory is allocated for static variables and constants.",
            codeHighlight: false,
            dataHighlight: true,
            heapHighlight: null,
            stackHighlight: null
        },
        {
            id: 5,
            title: "Main Method Execution",
            description: "The program's main method begins execution, creating stack frame.",
            codeHighlight: true,
            dataHighlight: false,
            heapHighlight: null,
            stackHighlight: true
        },
        {
            id: 6,
            title: "Object Creation",
            description: "Objects are created and allocated on the heap.",
            codeHighlight: false,
            dataHighlight: false,
            heapHighlight: true,
            stackHighlight: true
        },
        {
            id: 7,
            title: "Method Calls",
            description: "Methods are called, creating new stack frames.",
            codeHighlight: true,
            dataHighlight: false,
            heapHighlight: true,
            stackHighlight: true
        },
        {
            id: 8,
            title: "Garbage Collection",
            description: "Unused objects are reclaimed by the garbage collector.",
            codeHighlight: false,
            dataHighlight: false,
            heapHighlight: true,
            stackHighlight: false
        },
        {
            id: 9,
            title: "Program Termination",
            description: "Program finishes execution, memory resources are released.",
            codeHighlight: true,
            dataHighlight: true,
            heapHighlight: true,
            stackHighlight: true
        }
    ]
};

// Function to load data (simulates an API call or file loading)
function loadData() {
    return new Promise((resolve) => {
        // Simulate loading delay
        setTimeout(() => {
            resolve(memoryLayoutData);
        }, 500);
    });
}
