// Compute the angular scale factor: from value to radians.
var k = ((typeof endAngle === "function"
    ? endAngle.apply(this, arguments)
    : endAngle) - startAngle)
    / d3.sum(values);