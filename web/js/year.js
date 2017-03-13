$(document).ready(function () {

    var title= "test";

    var labels = [];
    var values = [];
    var colors = [];

    $.each(listSumByMonth, function(date, row) {
        labels.push(row.category);
        values.push(row.amount);
        colors.push(Utilities.getCategoryColor(row.category));
    });

    Utilities.createChart(labels, values, colors, title);

    labels = [];
    values = [];
    colors = [];

    $.each(listSumBySubCategory, function(date, row) {
        labels.push(row.subcategory);
        values.push(row.amount);
        colors.push(Utilities.getCategoryColor(row.category));
    });

    Utilities.createChart(labels, values, colors, title);

});