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
    const table = $('#memorytable');
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
    
    // Update memory sections
    updateMemorySection('code-segment', stepData.codeHighlight);
    updateMemorySection('data-segment', stepData.dataHighlight);
    updateMemorySection('heap', stepData.heapHighlight);
    updateMemorySection('stack', stepData.stackHighlight);
    
    // Update process visualization
    updateProcessVisualization(step);
    
    // Update memory table
    updateMemoryTable(step);
}

// Update a memory section's appearance based on whether it's highlighted
function updateMemorySection(sectionId, isHighlighted) {
    const section = $('#' + sectionId);
    
    section.removeClass('active-section');
    
    if (isHighlighted) {
        section.addClass('active-section');
        section.css({
            'border-color': '#0d47a1',
            'box-shadow': '0 0 8px rgba(13, 71, 161, 0.5)',
            'transform': 'translateX(10px)'
        });
    } else {
        section.css({
            'border-color': '#ccc',
            'box-shadow': 'none',
            'transform': 'translateX(0)'
        });
    }
}

// Update the process visualization based on the current step
function updateProcessVisualization(step) {
    const steps = $('.step-box');
    
    steps.removeClass('active-step');
    
    if (step >= 1) {
        $(steps[0]).addClass('active-step');
        $(steps[0]).css('background-color', '#b3e5fc');
    } else {
        $(steps[0]).css('background-color', '#e0f7fa');
    }
    
    if (step >= 2) {
        $(steps[1]).addClass('active-step');
        $(steps[1]).css('background-color', '#b3e5fc');
    } else {
        $(steps[1]).css('background-color', '#e0f7fa');
    }
    
    if (step >= 3) {
        $(steps[2]).addClass('active-step');
        $(steps[2]).css('background-color', '#b3e5fc');
    } else {
        $(steps[2]).css('background-color', '#e0f7fa');
    }
}

// Update the memory table based on the current step
function updateMemoryTable(step) {
    // Reset all blocks
    for (let i = 0; i < 10; i++) {
        $(`#mem-block-${i}`).css('background-color', '#f8f9fa');
    }
    
    // Color blocks based on the step
    if (step >= 3) { // Code section
        $('#mem-block-0, #mem-block-1').css('background-color', '#e3f2fd');
    }
    
    if (step >= 4) { // Data section
        $('#mem-block-2, #mem-block-3').css('background-color', '#e8f5e9');
    }
    
    if (step >= 6) { // Heap section
        $('#mem-block-4, #mem-block-5, #mem-block-6').css('background-color', '#fff3e0');
    }
    
    if (step >= 5) { // Stack section
        $('#mem-block-7, #mem-block-8, #mem-block-9').css('background-color', '#f3e5f5');
    }
    
    // Show garbage collection
    if (step === 8) {
        $('#mem-block-6').css('background-color', '#ffcdd2');
    }
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
    
    // Create visual blocks for the process
    for (let i = 0; i < processSize; i++) {
        blocks += "<td " + "id=" + (process.name + (i + 1)) + 
                " style=\"width: 30px; height: 30px; background-color:" + 
                process.color + ";border: 1px solid;\"><\/td>";
    }
    
    // Add process visualization to the animation area
    $('#processarea').append(
        "<span id=\"step\" style=\"display: inline; float: left; margin:" + 
        (190 + (70 * currStep)) + "px 0px 0px -280px;\">" + process.name + 
        "<\/span>\r\n<table id=\'processblock\' style=\"border: 0px solid;display: inline; float: left; margin: " + 
        (220 + (70 * currStep)) + "px 0px 0px -280px;\">\r\n<tr>\r\n" + 
        blocks + "\r\n<\/tr>\r\n<\/table>"
    );
    
    // Animate each block moving into memory
    let startAddr = parseInt(process.startAddress);
    for (let i = 0; i < processSize; i++) {
        moveProcessToMemory(process.name + (i + 1), startAddr + i, i);
    }
    
    // Increment step counter
    currStep++;
    
    // Check if animation is complete
    if (processes.length == currStep) {
        completed();
    }


// Animates moving a process block to its memory location
function moveProcessToMemory(processBlock, memoryBlock, blockIndex) {
    animLock = true;
    
    var clonedBlock = $('#' + processBlock).clone();
    var cloned = "<div id=\"" + (processBlock + 'cloned') + 
                "\" style=\"display: inline-block;position: absolute;height: 30px; width:30px;border: 1px solid;\"></div>";
    
    $('#' + processBlock).append(cloned);
    
    animList.push({ 
        org: $('#' + processBlock), 
        proc: clonedBlock, 
        mem: memoryBlock, 
        top: clonedBlock.position().top 
    });
    
    $('#' + (processBlock + 'cloned')).css('background-color', processes[currStep].color);
    
    $('#' + (processBlock + 'cloned')).animate({ 
        left: memtblpos.left + $('#' + memoryBlock).position().left + 100, 
        top: memtblpos.top + $('#' + memoryBlock).position().top, 
        width: 50, 
        height: 50 
    }, animSpeed, function () {
        animLock = false;
        if (blockIndex > 0) return;
        if (currStep > totalProcesses) return;
        animate(0);
    });
}

// Undoes the last animation step
function Undo() {
    animLock = true;
    if (currStep > 0) {
        // Counts the step back
        currStep--;
        
        // Removes the block and its corresponding text
        $("#processarea").children().last().remove();
        $("#processarea").children().last().remove();
        
        // Updates the "Step: " display
        $('#step').html("Step: " + (currStep));
    }
    animLock = false;
}
