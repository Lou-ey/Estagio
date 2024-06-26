var opts = {
    angle: 0, // The span of the gauge arc
    lineWidth: 0.3, // The line thickness
    radiusScale: 1, // Relative radius
    pointer: {
        length: 0.51, // // Relative to gauge radius
        strokeWidth: 0.068, // The thickness
        color: '#000000' // Fill color
    },
    limitMax: false,     // If false, max value increases automatically if value > maxValue
    limitMin: false,     // If true, the min value of the gauge will be fixed
    colorStart: '#6FADCF',   // Colors
    colorStop: '#8FC0DA',    // just experiment with them
    strokeColor: '#E0E0E0',  // to see which ones work best for you
    generateGradient: true,
    highDpiSupport: true,     // High resolution support
    staticZones: [
        {strokeStyle: "#F03E3E", min: 0, max: 5}, // Red from 100 to 130
       {strokeStyle: "#FFDD00", min: 5, max: 15}, // Yellow
       {strokeStyle: "green", min: 15, max: 30}, // Green
       {strokeStyle: "#FFDD00", min: 30, max: 40}, // Yellow
       {strokeStyle: "#F03E3E", min: 40, max: 100}  // Red
    ],
    staticLabels: {
        font: "10px sans-serif",  // Specifies font
        labels: [-10, 0, 50, 100],  // Print labels at these values
        color: "#000000",  // Optional: Label text color
        fractionDigits: 0  // Optional: Numerical precision. 0=round off.
    },
};
var target = document.getElementById('temperature-gauge'); // your canvas element
var gauge_temp = new Gauge(target).setOptions(opts); // create sexy gauge!
gauge_temp.maxValue = 100; // set max gauge value
gauge_temp.setMinValue(0);  // Prefer setter over gauge.minValue = 0
gauge_temp.animationSpeed = 32; // set animation speed (32 is default value)
gauge_temp.set(27); // set actual value

var target = document.getElementById('humidity-gauge'); // your canvas element
var gauge_hum = new Gauge(target).setOptions(opts); // create sexy gauge!
gauge_hum.maxValue = 100; // set max gauge value
gauge_hum.setMinValue(0);  // Prefer setter over gauge.minValue = 0
gauge_hum.animationSpeed = 32; // set animation speed (32 is default value)
gauge_hum.set(27); // set actual value

var target = document.getElementById('noise-gauge'); // your canvas element
var gauge_hum = new Gauge(target).setOptions(opts); // create sexy gauge!
gauge_hum.maxValue = 100; // set max gauge value
gauge_hum.setMinValue(0);  // Prefer setter over gauge.minValue = 0
gauge_hum.animationSpeed = 32; // set animation speed (32 is default value)
gauge_hum.set(27); // set actual value

var target = document.getElementById('air-quality-gauge'); // your canvas element
var gauge_hum = new Gauge(target).setOptions(opts); // create sexy gauge!
gauge_hum.maxValue = 100; // set max gauge value
gauge_hum.setMinValue(0);  // Prefer setter over gauge.minValue = 0
gauge_hum.animationSpeed = 32; // set animation speed (32 is default value)
gauge_hum.set(27); // set actual value