import ApexCharts from "apexcharts";

export default class ApexChart {
    constructor(element, labels, data, options = {}) {
        this.element = element;
        this.labels = labels;
        this.numericLabels = Array.from(
            { length: data.length },
            (_, i) => i + 1
        );
        this.data = data;
        this.chart = null;
        this.defaultOptions = {
            chart: {
                height: 350,
                type: "area",
                background: "transparent",
                foreColor: "#9ba7b2",
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                    },
                    export: {
                        csv: {
                            filename: "score_data",
                            columnDelimiter: ",",
                            headerCategory: "Kriteria",
                            headerValue: "Skor",
                        },
                    },
                    style: {
                        backgroundColor: "#08080A", // Warna background toolbar
                        borderColor: "#334155", // Warna border toolbar
                        borderWidth: 1, // Lebar border
                        borderRadius: 4, // Sudut melengkung
                        padding: "6px 8px", // Padding dalam toolbar
                    },
                },
                offsetX: 0,
                offsetY: 0,
            },
            // theme: {
            //     mode: "dark",
            // },
            tooltip: {
                enabled: true,
                theme: "dark",
                x: {
                    formatter: (value, { dataPointIndex }) => {
                        // Tampilkan label asli di tooltip
                        return `${this.labels[dataPointIndex]}`;
                    },
                },
            },
            series: [
                {
                    name: "Jumlah Skor",
                    data: this.data,
                },
            ],
            stroke: {
                width: 5,
                curve: "straight",
            },
            fill: {
                type: "gradient",
                gradient: {
                    shade: "dark",
                    gradientToColors: ["#ED9E9A"],
                    shadeIntensity: 1,
                    type: "vertical",
                    opacityFrom: 0.8,
                    opacityTo: 0.1,
                    stops: [0, 100, 100, 100],
                },
            },
            colors: ["#ED9E9A"],
            dataLabels: {
                enabled: true,
            },
            xaxis: {
                categories: this.numericLabels,
                title: {
                    text: "Kriteria",
                    style: {
                        color: "#9ba7b2", // Warna judul
                    },
                },
                labels: {
                    show: true, // Pastikan labels ditampilkan
                    // rotate: -30, // Rotasi 30 derajat (negatif untuk miring ke kanan)
                    // rotateAlways: true, // Selalu rotasi meski cukup space
                    hideOverlappingLabels: false, // Tampilkan semua label tanpa ada yang disembunyikan
                    style: {
                        colors: "#9ba7b2", // Warna teks
                        fontSize: "12px", // Ukuran font
                        fontFamily: "Helvetica, Arial, sans-serif", // Jenis font
                        cssClass: "apexcharts-xaxis-label", // Class tambahan
                    },
                    formatter: function (value) {
                        return value; // Anda bisa memformat teks di sini jika perlu
                    },
                },
                axisTicks: {
                    show: false, // Sembunyikan ticks jika ingin tampilan lebih bersih
                },
                tooltip: {
                    enabled: false,
                },
            },
            yaxis: {
                title: {
                    text: "Skor",
                },
                min: 0,
            },
        };
        this.options = { ...this.defaultOptions, ...options };
    }

    render() {
        if (this.chart) {
            this.chart.destroy();
        }
        this.chart = new ApexCharts(this.element, this.options);
        this.chart.render();
        return this;
    }

    updateData(newData) {
        this.chart.updateSeries([
            {
                data: newData,
            },
        ]);
    }

    destroy() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
}
