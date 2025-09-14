/**
 * Memory Layout - Animation Module
 * This file handles the animations for the memory layout visualization
 */

// Global variables
let currentStep = 0;
let animationData = null;
let animationPlaying = false;
let animationInterval = null;
const ANIMATION_SPEED = 1500; // milliseconds between animation steps

// Initialize when the document is ready
$(document).ready(function() {
    // Hide the overlay initially
    $("#overlay").hide();
    
    // Load the data
    initializeAnimation();
    
    // Set up event listeners for animation controls
    setupEventListeners();
    
    // Initialize the memory table
    initializeMemoryTable();
});

// Initialize the animation by loading the data
async function initializeAnimation() {
    try {
        $("#overlay").show();
        animationData = await loadData();
        $("#overlay").hide();
        updateVisualization(0); // Start at step 0
    } catch (error) {
        console.error("Error loading animation data:", error);
        $("#overlay").hide();
        $("#description").html("<p class='text-danger'>Error loading animation data. Please try refreshing the page.</p>");
    }
}

// Set up event listeners for the animation control buttons
function setupEventListeners() {
    // Animation type radio buttons
    $('input[type=radio][name=animationType]').change(function() {
        if (this.value === 'StepByStep') {
            $('#play').prop('disabled', true);
            $('#next, #back').prop('disabled', false);
            stopAnimation();
        } else if (this.value === 'Automatic') {
            $('#play').prop('disabled', false);
            $('#next, #back').prop('disabled', true);
        }
    });
    
    // Control buttons
    $('#start').click(function() {
        currentStep = 0;
        updateVisualization(currentStep);
    });
    
    $('#back').click(function() {
        if (currentStep > 0) {
            currentStep--;
            updateVisualization(currentStep);
        }
    });
    
    $('#play').click(function() {
        if (animationPlaying) {
            stopAnimation();
            $(this).text('Play');
        } else {
            startAnimation();
            $(this).text('Pause');
        }
    });
    
    $('#next').click(function() {
        if (currentStep < animationData.steps.length - 1) {
            currentStep++;
            updateVisualization(currentStep);
        }
    });
    
    $('#end').click(function() {
        currentStep = animationData.steps.length - 1;
        updateVisualization(currentStep);
    });
    
    $('#reset').click(function() {
        stopAnimation();
        $('#play').text('Play');
        currentStep = 0;
        updateVisualization(currentStep);
    });
}

// Initialize the memory table with empty blocks
function initializeMemoryTable() {
    const table = $('#memorytable tbody');
    table.empty();
    
    const row = $('<tr></tr>');
    
    for (let i = 0; i < 10; i++) {
        const cell = $('<td></td>')
            .attr('id', 'mem-block-' + i)
            .attr('data-block', i)
            .text(i);
        row.append(cell);
    }
    
    table.append(row);
}

// Update the visualization based on the current step
function updateVisualization(step) {
    if (!animationData || !animationData.steps || animationData.steps.length === 0) {
        return;
    }
    
    const stepData = animationData.steps[step];
    
    // Update step info
    $('#step-info').text(`Step: ${step} - ${stepData.title}`);
    
    // Update description
    $('#description p').text(stepData.description);
    
    // Update code highlighting based on step
    updateCodeHighlighting(step);
    
    // Update memory sections with animation
    updateMemorySection('code-segment', stepData.codeHighlight);
    updateMemorySection('data-segment', stepData.dataHighlight);
    updateMemorySection('heap', stepData.heapHighlight);
    updateMemorySection('stack', stepData.stackHighlight);
    
    // Update process visualization
    updateProcessVisualization(step);
    
    // Update memory table
    updateMemoryTable(step);
}

// Function to highlight relevant code sections based on the current step
function updateCodeHighlighting(step) {
    // Reset all highlights
    $('.java-code-block code').html($('.java-code-block code').html().replace(/<span class=".*?-highlight">(.*?)<\/span>/g, "$1"));
    
    // Apply highlighting based on the current step
    if (step >= 1 && step <= 3) {
        // Highlight class definition and method definitions (Code Segment)
        highlightCodeSegment();
    }
    
    if (step >= 4) {
        // Highlight static variables (Data Segment)
        highlightDataSegment();
    }
    
    if (step >= 5) {
        // Highlight main method and local variables (Stack)
        highlightStackSegment();
    }
    
    if (step >= 6) {
        // Highlight object creation (Heap)
        highlightHeapSegment();
    }
}

function highlightCodeSegment() {
    let codeContent = $('.java-code-block code').html();
    
    // Highlight class definition and method signatures
    codeContent = codeContent.replace(
        /(public class A|public static int isum\(int i1, int i2\) {|public static void main\(String\[] args\) {)/g, 
        '<span class="code-highlight">$1</span>'
    );
    
    $('.java-code-block code').html(codeContent);
}

function highlightDataSegment() {
    let codeContent = $('.java-code-block code').html();
    
    // Highlight static variable
    codeContent = codeContent.replace(
        /(static int x = 1000;)/g, 
        '<span class="data-highlight">$1</span>'
    );
    
    $('.java-code-block code').html(codeContent);
}

function highlightStackSegment() {
    let codeContent = $('.java-code-block code').html();
    
    // Highlight method parameters and local variables
    codeContent = codeContent.replace(
        /(int i1, int i2|int result = isum\(5, 10\);)/g, 
        '<span class="stack-highlight">$1</span>'
    );
    
    $('.java-code-block code').html(codeContent);
}

function highlightHeapSegment() {
    let codeContent = $('.java-code-block code').html();
    
    // Highlight object creation
    codeContent = codeContent.replace(
        /(new String\("Result: " \+ result\);)/g, 
        '<span class="heap-highlight">$1</span>'
    );
    
    $('.java-code-block code').html(codeContent);
}

// Update a memory section's appearance based on whether it's highlighted
function updateMemorySection(sectionId, isHighlighted) {
    const section = $('#' + sectionId);
    
    if (isHighlighted) {
        // Animate the highlight effect
        section.css({
            'border-color': '#0d47a1',
            'box-shadow': '0 0 8px rgba(13, 71, 161, 0.5)'
        }).animate({
            'transform': 'translateX(10px)'
        }, 300);
        
        // Update content based on the section
        updateSectionContent(sectionId, currentStep);
    } else {
        // Animate back to normal
        section.animate({
            'transform': 'translateX(0)'
        }, 300).css({
            'border-color': '#ccc',
            'box-shadow': 'none'
        });
    }
}

// Update the content of a memory section based on the current step
function updateSectionContent(sectionId, step) {
    const contentDiv = $(`#${sectionId} .mem-content`);
    
    switch(sectionId) {
        case 'code-segment':
            if (step >= 1 && step <= 3) {
                contentDiv.html('Loading program instructions...');
            } else if (step > 3) {
                contentDiv.html('Class A<br>Method isum()<br>Method main()');
            }
            break;
            
        case 'data-segment':
            if (step === 4) {
                contentDiv.html('Allocating static variables...');
            } else if (step > 4) {
                contentDiv.html('static int x = 1000<br>Constants<br>Class metadata');
            }
            break;
            
        case 'heap':
            if (step === 6) {
                contentDiv.html('Creating String object...');
            } else if (step === 8) {
                contentDiv.html('Garbage collection running...');
            } else if (step > 6) {
                contentDiv.html('String "Result: 15"<br>Other objects<br>Managed by GC');
            }
            break;
            
        case 'stack':
            if (step === 5) {
                contentDiv.html('Creating main() stack frame...');
            } else if (step === 7) {
                contentDiv.html('Stack frame for isum()<br>Parameters i1=5, i2=10');
            } else if (step > 7) {
                contentDiv.html('Local variable result=15<br>Return addresses<br>Method parameters');
            }
            break;
    }
}

// Update the process visualization based on the current step
function updateProcessVisualization(step) {
    // Reset all steps
    $('.step-box').css({
        'background-color': '#e0f7fa',
        'transform': 'scale(1)',
        'box-shadow': 'none'
    });
    
    // Highlight active steps with animation effects
    if (step >= 1) {
        highlightStep(0); // Source Code
    }
    
    if (step >= 2) {
        highlightStep(1); // Compilation
    }
    
    if (step >= 3) {
        highlightStep(2); // Bytecode
    }
}

// Highlight a specific step in the process visualization
function highlightStep(stepIndex) {
    $(`.step-box:eq(${stepIndex})`).css({
        'background-color': '#b3e5fc',
        'transform': 'scale(1.05)',
        'box-shadow': '0 4px 8px rgba(0, 0, 0, 0.1)'
    });
}

// Update the memory table based on the current step
function updateMemoryTable(step) {
    // Reset all blocks
    for (let i = 0; i < 10; i++) {
        $(`#mem-block-${i}`).css({
            'background-color': '#f8f9fa',
            'color': '#000',
            'font-weight': 'normal'
        }).text(i);
    }
    
    // Color blocks based on the step with animations
    if (step >= 3) { // Code section
        animateMemoryBlocks([0, 1], '#e3f2fd', 'CODE');
    }
    
    if (step >= 4) { // Data section
        animateMemoryBlocks([2, 3], '#e8f5e9', 'DATA');
    }
    
    if (step >= 6) { // Heap section
        animateMemoryBlocks([4, 5, 6], '#fff3e0', 'HEAP');
    }
    
    if (step >= 5) { // Stack section
        animateMemoryBlocks([7, 8, 9], '#f3e5f5', 'STACK');
    }
    
    // Show garbage collection
    if (step === 8) {
        $('#mem-block-6').css('background-color', '#ffcdd2').text('GC');
    }
    
    // Show program termination
    if (step === 9) {
        for (let i = 0; i < 10; i++) {
            $(`#mem-block-${i}`).css('background-color', '#f5f5f5').text('FREE');
        }
    }
}

// Animate the coloring of memory blocks
function animateMemoryBlocks(blockIndices, color, label) {
    blockIndices.forEach(index => {
        $(`#mem-block-${index}`)
            .animate({ backgroundColor: color }, 500)
            .text(label)
            .css({
                'color': '#000',
                'font-weight': 'bold',
                'font-size': '0.8em'
            });
    });
}

// Start the automatic animation
function startAnimation() {
    if (animationPlaying) return;
    
    animationPlaying = true;
    
    animationInterval = setInterval(() => {
        if (currentStep < animationData.steps.length - 1) {
            currentStep++;
            updateVisualization(currentStep);
        } else {
            stopAnimation();
            $('#play').text('Play');
        }
    }, ANIMATION_SPEED);
}

// Stop the automatic animation
function stopAnimation() {
    if (!animationPlaying) return;
    
    animationPlaying = false;
    clearInterval(animationInterval);
}
