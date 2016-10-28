$(document).ready(function () {

    var labels = [];
    var values = [];

    $.each(listSumByMonth, function(i, row) {
        labels.push(row.date);
        values.push(row.amount);
    });

    Utilities.createChart(labels, values);
});