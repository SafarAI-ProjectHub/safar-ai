$(function () {
  "use strict";
  console.log('Yearly Payments Cliq:', yearlyPaymentsCliq);
  console.log('Yearly Payments PayPal:', yearlyPaymentsPaypal);


  // chart 1
  var ctx1 = document.getElementById("chart1").getContext('2d');

  var gradientStroke1 = ctx1.createLinearGradient(0, 0, 0, 300);
  gradientStroke1.addColorStop(0, '#6078ea');
  gradientStroke1.addColorStop(1, '#17c5ea');

  var gradientStroke2 = ctx1.createLinearGradient(0, 0, 0, 300);
  gradientStroke2.addColorStop(0, '#ff8359');
  gradientStroke2.addColorStop(1, '#ffdf40');

  var gradientStroke3 = ctx1.createLinearGradient(0, 0, 0, 300);
  gradientStroke3.addColorStop(0, '#ff8359');
  gradientStroke3.addColorStop(1, '#e67d5a');

  var myChart1 = new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: [{
        label: 'Students',
        data: yearlyStudents,
        borderColor: gradientStroke1,
        backgroundColor: gradientStroke1,
        hoverBackgroundColor: gradientStroke1,
        pointRadius: 0,
        fill: false,
        borderRadius: 20,
        borderWidth: 0
      }, {
        label: 'Subscriptions',
        data: yearlySubscriptions,
        borderColor: gradientStroke2,
        backgroundColor: gradientStroke2,
        hoverBackgroundColor: gradientStroke2,
        pointRadius: 0,
        fill: false,
        borderRadius: 20,
        borderWidth: 0
      }, {
        label: 'Payments',
        data: yearlyPayments,
        borderColor: gradientStroke3,
        backgroundColor: gradientStroke3,
        hoverBackgroundColor: gradientStroke3,
        pointRadius: 0,
        fill: false,
        borderRadius: 20,
        borderWidth: 0
      }]
    },
    options: {
      maintainAspectRatio: false,
      barPercentage: 0.5,
      categoryPercentage: 0.8,
      plugins: {
        legend: {
          display: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // chart 2
  var ctx2 = document.getElementById("chart2").getContext('2d');

  var gradientStroke1 = ctx2.createLinearGradient(0, 0, 0, 300);
  gradientStroke1.addColorStop(0, '#fc4a1a');
  gradientStroke1.addColorStop(1, '#f7b733');

  var gradientStroke2 = ctx2.createLinearGradient(0, 0, 0, 300);
  gradientStroke2.addColorStop(0, '#003087');
  gradientStroke2.addColorStop(1, '#009cde');

  var myChart2 = new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: ["Cliq", "PayPal"],
      datasets: [{
        backgroundColor: [
          gradientStroke1,
          gradientStroke2
        ],
        hoverBackgroundColor: [
          gradientStroke1,
          gradientStroke2
        ],
        data: [yearlyPaymentsCliq, yearlyPaymentsPaypal],
        borderWidth: [1, 1]
      }]
    },
    options: {
      maintainAspectRatio: false,
      cutout: 82,
      plugins: {
        legend: {
          display: false,
        }
      }
    }
  });




  // worl map


  var markers = [];
  var regionColors = {};

  var countryCodeMapping = {
    'Afghanistan': 'AF',
    'Albania': 'AL',
    'Algeria': 'DZ',
    'Andorra': 'AD',
    'Angola': 'AO',
    'Antigua and Barbuda': 'AG',
    'Argentina': 'AR',
    'Armenia': 'AM',
    'Australia': 'AU',
    'Austria': 'AT',
    'Azerbaijan': 'AZ',
    'Bahamas': 'BS',
    'Bahrain': 'BH',
    'Bangladesh': 'BD',
    'Barbados': 'BB',
    'Belarus': 'BY',
    'Belgium': 'BE',
    'Belize': 'BZ',
    'Benin': 'BJ',
    'Bhutan': 'BT',
    'Bolivia': 'BO',
    'Bosnia and Herzegovina': 'BA',
    'Botswana': 'BW',
    'Brazil': 'BR',
    'Brunei': 'BN',
    'Bulgaria': 'BG',
    'Burkina Faso': 'BF',
    'Burundi': 'BI',
    'Cabo Verde': 'CV',
    'Cambodia': 'KH',
    'Cameroon': 'CM',
    'Canada': 'CA',
    'Central African Republic': 'CF',
    'Chad': 'TD',
    'Chile': 'CL',
    'China': 'CN',
    'Colombia': 'CO',
    'Comoros': 'KM',
    'Congo, Democratic Republic of the': 'CD',
    'Congo, Republic of the': 'CG',
    'Costa Rica': 'CR',
    'Croatia': 'HR',
    'Cuba': 'CU',
    'Cyprus': 'CY',
    'Czech Republic': 'CZ',
    'Denmark': 'DK',
    'Djibouti': 'DJ',
    'Dominica': 'DM',
    'Dominican Republic': 'DO',
    'Ecuador': 'EC',
    'Egypt': 'EG',
    'El Salvador': 'SV',
    'Equatorial Guinea': 'GQ',
    'Eritrea': 'ER',
    'Estonia': 'EE',
    'Eswatini': 'SZ',
    'Ethiopia': 'ET',
    'Fiji': 'FJ',
    'Finland': 'FI',
    'France': 'FR',
    'Gabon': 'GA',
    'Gambia': 'GM',
    'Georgia': 'GE',
    'Germany': 'DE',
    'Ghana': 'GH',
    'Greece': 'GR',
    'Grenada': 'GD',
    'Guatemala': 'GT',
    'Guinea': 'GN',
    'Guinea-Bissau': 'GW',
    'Guyana': 'GY',
    'Haiti': 'HT',
    'Honduras': 'HN',
    'Hungary': 'HU',
    'Iceland': 'IS',
    'India': 'IN',
    'Indonesia': 'ID',
    'Iran': 'IR',
    'Iraq': 'IQ',
    'Ireland': 'IE',
    'Israel': 'IL',
    'Italy': 'IT',
    'Jamaica': 'JM',
    'Japan': 'JP',
    'Jordan': 'JO',
    'Kazakhstan': 'KZ',
    'Kenya': 'KE',
    'Kiribati': 'KI',
    'Korea, North': 'KP',
    'Korea, South': 'KR',
    'Kuwait': 'KW',
    'Kyrgyzstan': 'KG',
    'Laos': 'LA',
    'Latvia': 'LV',
    'Lebanon': 'LB',
    'Lesotho': 'LS',
    'Liberia': 'LR',
    'Libya': 'LY',
    'Liechtenstein': 'LI',
    'Lithuania': 'LT',
    'Luxembourg': 'LU',
    'Madagascar': 'MG',
    'Malawi': 'MW',
    'Malaysia': 'MY',
    'Maldives': 'MV',
    'Mali': 'ML',
    'Malta': 'MT',
    'Marshall Islands': 'MH',
    'Mauritania': 'MR',
    'Mauritius': 'MU',
    'Mexico': 'MX',
    'Micronesia': 'FM',
    'Moldova': 'MD',
    'Monaco': 'MC',
    'Mongolia': 'MN',
    'Montenegro': 'ME',
    'Morocco': 'MA',
    'Mozambique': 'MZ',
    'Myanmar': 'MM',
    'Namibia': 'NA',
    'Nauru': 'NR',
    'Nepal': 'NP',
    'Netherlands': 'NL',
    'New Zealand': 'NZ',
    'Nicaragua': 'NI',
    'Niger': 'NE',
    'Nigeria': 'NG',
    'North Macedonia': 'MK',
    'Norway': 'NO',
    'Oman': 'OM',
    'Pakistan': 'PK',
    'Palau': 'PW',
    'Panama': 'PA',
    'Papua New Guinea': 'PG',
    'Paraguay': 'PY',
    'Peru': 'PE',
    'Philippines': 'PH',
    'Poland': 'PL',
    'Portugal': 'PT',
    'Qatar': 'QA',
    'Romania': 'RO',
    'Russia': 'RU',
    'Rwanda': 'RW',
    'Saint Kitts and Nevis': 'KN',
    'Saint Lucia': 'LC',
    'Saint Vincent and the Grenadines': 'VC',
    'Samoa': 'WS',
    'San Marino': 'SM',
    'Sao Tome and Principe': 'ST',
    'Saudi Arabia': 'SA',
    'Senegal': 'SN',
    'Serbia': 'RS',
    'Seychelles': 'SC',
    'Sierra Leone': 'SL',
    'Singapore': 'SG',
    'Slovakia': 'SK',
    'Slovenia': 'SI',
    'Solomon Islands': 'SB',
    'Somalia': 'SO',
    'South Africa': 'ZA',
    'South Sudan': 'SS',
    'Spain': 'ES',
    'Sri Lanka': 'LK',
    'Sudan': 'SD',
    'Suriname': 'SR',
    'Sweden': 'SE',
    'Switzerland': 'CH',
    'Syria': 'SY',
    'Taiwan': 'TW',
    'Tajikistan': 'TJ',
    'Tanzania': 'TZ',
    'Thailand': 'TH',
    'Timor-Leste': 'TL',
    'Togo': 'TG',
    'Tonga': 'TO',
    'Trinidad and Tobago': 'TT',
    'Tunisia': 'TN',
    'Turkey': 'TR',
    'Turkmenistan': 'TM',
    'Tuvalu': 'TV',
    'Uganda': 'UG',
    'Ukraine': 'UA',
    'United Arab Emirates': 'AE',
    'United Kingdom': 'GB',
    'United States': 'US',
    'Uruguay': 'UY',
    'Uzbekistan': 'UZ',
    'Vanuatu': 'VU',
    'Vatican City': 'VA',
    'Venezuela': 'VE',
    'Vietnam': 'VN',
    'Yemen': 'YE',
    'Zambia': 'ZM',
    'Zimbabwe': 'ZW'
  };

  var countryCoordinates = {
    'AF': { lat: 33.9391, lng: 67.7100 },
    'AL': { lat: 41.1533, lng: 20.1683 },
    'DZ': { lat: 28.0339, lng: 1.6596 },
    'AD': { lat: 42.5063, lng: 1.5218 },
    'AO': { lat: -11.2027, lng: 17.8739 },
    'AG': { lat: 17.0608, lng: -61.7964 },
    'AR': { lat: -38.4161, lng: -63.6167 },
    'AM': { lat: 40.0691, lng: 45.0382 },
    'AU': { lat: -25.2744, lng: 133.7751 },
    'AT': { lat: 47.5162, lng: 14.5501 },
    'AZ': { lat: 40.1431, lng: 47.5769 },
    'BS': { lat: 25.0343, lng: -77.3963 },
    'BH': { lat: 25.9304, lng: 50.6378 },
    'BD': { lat: 23.6850, lng: 90.3563 },
    'BB': { lat: 13.1939, lng: -59.5432 },
    'BY': { lat: 53.7098, lng: 27.9534 },
    'BE': { lat: 50.8503, lng: 4.3517 },
    'BZ': { lat: 17.1899, lng: -88.4976 },
    'BJ': { lat: 9.3077, lng: 2.3158 },
    'BT': { lat: 27.5142, lng: 90.4336 },
    'BO': { lat: -16.2902, lng: -63.5887 },
    'BA': { lat: 43.9159, lng: 17.6791 },
    'BW': { lat: -22.3285, lng: 24.6849 },
    'BR': { lat: -14.2350, lng: -51.9253 },
    'BN': { lat: 4.5353, lng: 114.7277 },
    'BG': { lat: 42.7339, lng: 25.4858 },
    'BF': { lat: 12.2383, lng: -1.5616 },
    'BI': { lat: -3.3731, lng: 29.9189 },
    'CV': { lat: 16.5388, lng: -23.0418 },
    'KH': { lat: 12.5657, lng: 104.9910 },
    'CM': { lat: 7.3697, lng: 12.3547 },
    'CA': { lat: 56.1304, lng: -106.3468 },
    'CF': { lat: 6.6111, lng: 20.9394 },
    'TD': { lat: 15.4542, lng: 18.7322 },
    'CL': { lat: -35.6751, lng: -71.5430 },
    'CN': { lat: 35.8617, lng: 104.1954 },
    'CO': { lat: 4.5709, lng: -74.2973 },
    'KM': { lat: -11.8750, lng: 43.8722 },
    'CD': { lat: -4.0383, lng: 21.7587 },
    'CG': { lat: -0.2280, lng: 15.8277 },
    'CR': { lat: 9.7489, lng: -83.7534 },
    'HR': { lat: 45.1000, lng: 15.2000 },
    'CU': { lat: 21.5218, lng: -77.7812 },
    'CY': { lat: 35.1264, lng: 33.4299 },
    'CZ': { lat: 49.8175, lng: 15.4730 },
    'DK': { lat: 56.2639, lng: 9.5018 },
    'DJ': { lat: 11.8251, lng: 42.5903 },
    'DM': { lat: 15.4150, lng: -61.3710 },
    'DO': { lat: 18.7357, lng: -70.1627 },
    'EC': { lat: -1.8312, lng: -78.1834 },
    'EG': { lat: 26.8206, lng: 30.8025 },
    'SV': { lat: 13.7942, lng: -88.8965 },
    'GQ': { lat: 1.6508, lng: 10.2679 },
    'ER': { lat: 15.1794, lng: 39.7823 },
    'EE': { lat: 58.5953, lng: 25.0136 },
    'SZ': { lat: -26.5225, lng: 31.4659 },
    'ET': { lat: 9.1450, lng: 40.4897 },
    'FJ': { lat: -17.7134, lng: 178.0650 },
    'FI': { lat: 61.9241, lng: 25.7482 },
    'FR': { lat: 46.6034, lng: 1.8883 },
    'GA': { lat: -0.8037, lng: 11.6094 },
    'GM': { lat: 13.4432, lng: -15.3101 },
    'GE': { lat: 32.1656, lng: -82.9001 },
    'DE': { lat: 51.1657, lng: 10.4515 },
    'GH': { lat: 7.9465, lng: -1.0232 },
    'GR': { lat: 39.0742, lng: 21.8243 },
    'GD': { lat: 12.1165, lng: -61.6790 },
    'GT': { lat: 15.7835, lng: -90.2308 },
    'GN': { lat: 9.9456, lng: -9.6966 },
    'GW': { lat: 11.8037, lng: -15.1804 },
    'GY': { lat: 4.8604, lng: -58.9302 },
    'HT': { lat: 18.9712, lng: -72.2852 },
    'HN': { lat: 15.1999, lng: -86.2419 },
    'HU': { lat: 47.1625, lng: 19.5033 },
    'IS': { lat: 64.9631, lng: -19.0208 },
    'IN': { lat: 20.5937, lng: 78.9629 },
    'ID': { lat: -0.7893, lng: 113.9213 },
    'IR': { lat: 32.4279, lng: 53.6880 },
    'IQ': { lat: 33.2232, lng: 43.6793 },
    'IE': { lat: 53.4129, lng: -8.2439 },
    'IL': { lat: 31.0461, lng: 34.8516 },
    'IT': { lat: 41.8719, lng: 12.5674 },
    'JM': { lat: 18.1096, lng: -77.2975 },
    'JP': { lat: 36.2048, lng: 138.2529 },
    'JO': { lat: 30.5852, lng: 36.2384 },
    'KZ': { lat: 48.0196, lng: 66.9237 },
    'KE': { lat: -0.0236, lng: 37.9062 },
    'KI': { lat: -3.3704, lng: -168.7340 },
    'KP': { lat: 40.3399, lng: 127.5101 },
    'KR': { lat: 35.9078, lng: 127.7669 },
    'KW': { lat: 29.3117, lng: 47.4818 },
    'KG': { lat: 41.2044, lng: 74.7661 },
    'LA': { lat: 19.8563, lng: 102.4955 },
    'LV': { lat: 56.8796, lng: 24.6032 },
    'LB': { lat: 33.8547, lng: 35.8623 },
    'LS': { lat: -29.6099, lng: 28.2336 },
    'LR': { lat: 6.4281, lng: -9.4295 },
    'LY': { lat: 26.3351, lng: 17.2283 },
    'LI': { lat: 47.1660, lng: 9.5554 },
    'LT': { lat: 55.1694, lng: 23.8813 },
    'LU': { lat: 49.8153, lng: 6.1296 },
    'MG': { lat: -18.7669, lng: 46.8691 },
    'MW': { lat: -13.2543, lng: 34.3015 },
    'MY': { lat: 4.2105, lng: 101.9758 },
    'MV': { lat: 3.2028, lng: 73.2207 },
    'ML': { lat: 17.5707, lng: -3.9962 },
    'MT': { lat: 35.9375, lng: 14.3754 },
    'MH': { lat: 7.1315, lng: 171.1845 },
    'MR': { lat: 21.0079, lng: -10.9408 },
    'MU': { lat: -20.3484, lng: 57.5522 },
    'MX': { lat: 23.6345, lng: -102.5528 },
    'FM': { lat: 7.4256, lng: 150.5508 },
    'MD': { lat: 47.4116, lng: 28.3699 },
    'MC': { lat: 43.7503, lng: 7.4128 },
    'MN': { lat: 46.8625, lng: 103.8467 },
    'ME': { lat: 42.7087, lng: 19.3744 },
    'MA': { lat: 31.7917, lng: -7.0926 },
    'MZ': { lat: -18.6657, lng: 35.5296 },
    'MM': { lat: 21.9162, lng: 95.9560 },
    'NA': { lat: -22.9576, lng: 18.4904 },
    'NR': { lat: -0.5228, lng: 166.9315 },
    'NP': { lat: 28.3949, lng: 84.1240 },
    'NL': { lat: 52.1326, lng: 5.2913 },
    'NZ': { lat: -40.9006, lng: 174.8860 },
    'NI': { lat: 12.8654, lng: -85.2072 },
    'NE': { lat: 17.6078, lng: 8.0817 },
    'NG': { lat: 9.0820, lng: 8.6753 },
    'MK': { lat: 41.6086, lng: 21.7453 },
    'NO': { lat: 60.4720, lng: 8.4689 },
    'OM': { lat: 21.5126, lng: 55.9233 },
    'PK': { lat: 30.3753, lng: 69.3451 },
    'PW': { lat: 7.5149, lng: 134.5825 },
    'PA': { lat: 8.5380, lng: -80.7821 },
    'PG': { lat: -6.3149, lng: 143.9555 },
    'PY': { lat: -23.4425, lng: -58.4438 },
    'PE': { lat: -9.1900, lng: -75.0152 },
    'PH': { lat: 12.8797, lng: 121.7740 },
    'PL': { lat: 51.9194, lng: 19.1451 },
    'PT': { lat: 39.3999, lng: -8.2245 },
    'QA': { lat: 25.3548, lng: 51.1839 },
    'RO': { lat: 45.9432, lng: 24.9668 },
    'RU': { lat: 61.5240, lng: 105.3188 },
    'RW': { lat: -1.9403, lng: 29.8739 },
    'KN': { lat: 17.3578, lng: -62.7829 },
    'LC': { lat: 13.9094, lng: -60.9789 },
    'VC': { lat: 12.9843, lng: -61.2872 },
    'WS': { lat: -13.7590, lng: -172.1046 },
    'SM': { lat: 43.9424, lng: 12.4578 },
    'ST': { lat: 0.1864, lng: 6.6131 },
    'SA': { lat: 23.8859, lng: 45.0792 },
    'SN': { lat: 14.4974, lng: -14.4524 },
    'RS': { lat: 44.0165, lng: 21.0059 },
    'SC': { lat: -4.6796, lng: 55.4910 },
    'SL': { lat: 8.4606, lng: -11.7799 },
    'SG': { lat: 1.3521, lng: 103.8198 },
    'SK': { lat: 48.6690, lng: 19.6990 },
    'SI': { lat: 46.1512, lng: 14.9955 },
    'SB': { lat: -9.6457, lng: 160.1562 },
    'SO': { lat: 5.1521, lng: 46.1996 },
    'ZA': { lat: -30.5595, lng: 22.9375 },
    'SS': { lat: 6.8770, lng: 31.3070 },
    'ES': { lat: 40.4637, lng: -3.7492 },
    'LK': { lat: 7.8731, lng: 80.7718 },
    'SD': { lat: 12.8628, lng: 30.2176 },
    'SR': { lat: 3.9193, lng: -56.0278 },
    'SE': { lat: 60.1282, lng: 18.6435 },
    'CH': { lat: 46.8182, lng: 8.2275 },
    'SY': { lat: 34.8021, lng: 38.9968 },
    'TW': { lat: 23.6978, lng: 120.9605 },
    'TJ': { lat: 38.8610, lng: 71.2761 },
    'TZ': { lat: -6.3690, lng: 34.8888 },
    'TH': { lat: 15.8700, lng: 100.9925 },
    'TL': { lat: -8.8742, lng: 125.7275 },
    'TG': { lat: 8.6195, lng: 0.8248 },
    'TO': { lat: -21.1789, lng: -175.1982 },
    'TT': { lat: 10.6918, lng: -61.2225 },
    'TN': { lat: 33.8869, lng: 9.5375 },
    'TR': { lat: 38.9637, lng: 35.2433 },
    'TM': { lat: 38.9697, lng: 59.5563 },
    'TV': { lat: -7.1095, lng: 177.6493 },
    'UG': { lat: 1.3733, lng: 32.2903 },
    'UA': { lat: 48.3794, lng: 31.1656 },
    'AE': { lat: 23.4241, lng: 53.8478 },
    'GB': { lat: 55.3781, lng: -3.4360 },
    'US': { lat: 37.0902, lng: -95.7129 },
    'UY': { lat: -32.5228, lng: -55.7658 },
    'UZ': { lat: 41.3775, lng: 64.5853 },
    'VU': { lat: -15.3767, lng: 166.9592 },
    'VA': { lat: 41.9029, lng: 12.4534 },
    'VE': { lat: 6.4238, lng: -66.5897 },
    'VN': { lat: 14.0583, lng: 108.2772 },
    'YE': { lat: 15.5527, lng: 48.5164 },
    'ZM': { lat: -13.1339, lng: 27.8493 },
    'ZW': { lat: -19.0154, lng: 29.1549 }
  };


  $.each(countryData, function (country, counts) {
    var total = counts.students + counts.teachers;
    var color = '#e4ecef'; // Default color
    if (counts.students > 0 && counts.teachers > 0) {
      color = '#ff8359'; // Color for both students and teachers
    } else if (counts.students > 0) {
      color = '#6078ea'; // Color for students only
    } else if (counts.teachers > 0) {
      color = '#ffc107'; // Color for teachers only
    }

    var countryCode = countryCodeMapping[country];
    if (countryCode) {
      regionColors[countryCode] = color;
      if (countryCoordinates[countryCode]) {
        markers.push({
          latLng: [countryCoordinates[countryCode].lat, countryCoordinates[countryCode].lng],
          name: country + ' - Students: ' + counts.students + ', Teachers: ' + counts.teachers,
          style: { fill: color }
        });
      }
    }
  });

  $('#geographic-map-2').vectorMap({
    map: 'world_mill_en',
    backgroundColor: 'transparent',
    borderColor: '#818181',
    borderOpacity: 0.25,
    borderWidth: 1,
    zoomOnScroll: false,
    regionStyle: {
      initial: {
        fill: '#e4ecef'
      },
      hover: {
        fill: "#6c757d"
      }
    },
    markerStyle: {
      initial: {
        r: 9,
        'fill': '#fff',
        'fill-opacity': 1,
        'stroke': '#000',
        'stroke-width': 5,
        'stroke-opacity': 0.4
      },
    },
    series: {
      regions: [{
        values: regionColors,
        attribute: 'fill'
      }]
    },
    markers: markers,
    hoverOpacity: null,
    normalizeFunction: 'linear',
    scaleColors: ['#b6d6ff', '#005ace'],
    selectedColor: '#c9dfaf',
    selectedRegions: [],
    showTooltip: true,
  });



  // chart 3
  var ctx = document.getElementById('chart3').getContext('2d');

  var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
  gradientStroke1.addColorStop(0, '#00b09b');
  gradientStroke1.addColorStop(1, '#96c93d');


  var daysInMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();
  var labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);

  var myChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Revenue',
        data: dailyRevenue,
        backgroundColor: [
          gradientStroke1
        ],
        fill: {
          target: 'origin',
          above: 'rgb(21 202 32 / 15%)',
        },
        tension: 0.4,
        borderColor: [
          gradientStroke1
        ],
        borderWidth: 3
      }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });







  // chart 5

  var ctx = document.getElementById("chart5").getContext('2d');

  var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
  gradientStroke1.addColorStop(0, '#f54ea2');
  gradientStroke1.addColorStop(1, '#ff7676');

  var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
  gradientStroke2.addColorStop(0, '#42e695');
  gradientStroke2.addColorStop(1, '#3bb2b8');

  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: [1, 2, 3, 4, 5],
      datasets: [{
        label: 'Clothing',
        data: [40, 30, 60, 35, 60],
        borderColor: gradientStroke1,
        backgroundColor: gradientStroke1,
        hoverBackgroundColor: gradientStroke1,
        pointRadius: 0,
        fill: false,
        borderWidth: 1
      }, {
        label: 'Electronic',
        data: [50, 60, 40, 70, 35],
        borderColor: gradientStroke2,
        backgroundColor: gradientStroke2,
        hoverBackgroundColor: gradientStroke2,
        pointRadius: 0,
        fill: false,
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      barPercentage: 0.5,
      categoryPercentage: 0.8,
      plugins: {
        legend: {
          display: false,
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });




});
