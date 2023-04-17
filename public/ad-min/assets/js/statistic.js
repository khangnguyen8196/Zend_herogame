$(function () {
    pages.statistic.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    statistic: {
        init: function () {
            var me = this;
            $.jGrowl.defaults.closer = false;
            pages.common.setupCheckbox();
            pages.common.setupDatePicker();
            if (typeof error != "undefined" && error != "error") {
                $.jGrowl(error, {
                    theme: 'alert-styled-left bg-danger'
                });
            }
            $(document).on('click', '#statistic_btn', {}, function (e) {
                e.preventDefault();
                $("#statistic_frm").submit();
            });
            if (typeof order_chart_data != "undefined" && typeof revenue_chart_data != "undefined") {
                google.charts.load('current', {'packages': ['bar']});
                google.charts.setOnLoadCallback(pages.statistic.drawChart);
            }
        },
        /**
         * 
         */
        drawChart: function () {
            $("#seperate").show();
            var order_data = google.visualization.arrayToDataTable(order_chart_data);
            //order chart
            var order_options = {
                chart: {
                    title: order_chart_title,
                    subtitle: order_chart_sub_title
                },
                bars: 'vertical',
                vAxis: {format: 'decimal'},
                colors: ['rgb(56, 113, 207)', 'rgb(15, 157, 88)', 'rgb(186, 58, 47)', 'rgb(244, 180, 0)']
            };
            var order_chart = new google.charts.Bar(document.getElementById('order_chart'));
            order_chart.draw(order_data, google.charts.Bar.convertOptions(order_options));
            //revenue chart
            var revenue_data = google.visualization.arrayToDataTable(revenue_chart_data);
            var revenue_options = {
                chart: {
                    title: revenue_chart_title,
                    subtitle: revenue_chart_sub_title
                },
                bars: 'vertical',
                vAxis: {format: 'decimal'},
                colors: ['#1b9e77', '#d95f02', '#7570b3']
            };
            var revenue_chart = new google.charts.Bar(document.getElementById('revenue_chart'));
            revenue_chart.draw(revenue_data, google.charts.Bar.convertOptions(revenue_options));
        }
    }
});