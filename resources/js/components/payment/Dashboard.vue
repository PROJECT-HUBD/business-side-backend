<template>
  <div class="payment-dashboard">
    <div class="filters mb-4">
      <div class="row">
        <div class="col-md-9">
          <div class="d-flex align-items-center">
            <div class="date-range me-3">
              <div class="input-group">
                <span class="input-group-text">開始日期</span>
                <input type="date" class="form-control" v-model="filters.startDate" />
                <span class="input-group-text">結束日期</span>
                <input type="date" class="form-control" v-model="filters.endDate" />
              </div>
            </div>
            <div class="payment-method me-3">
              <select class="form-select" v-model="filters.paymentMethod">
                <option value="">所有支付方式</option>
                <option value="credit_card">信用卡</option>
                <option value="line_pay">Line Pay</option>
                <option value="bank_transfer">銀行轉賬</option>
                <option value="ecpay">綠界支付</option>
              </select>
            </div>
            <button class="btn btn-primary" @click="applyFilters">套用篩選</button>
          </div>
        </div>
        <div class="col-md-3 text-end">
          <button class="btn btn-outline-secondary me-2" @click="exportCsv">
            <i class="bi bi-file-earmark-text"></i> 匯出 CSV
          </button>
          <button class="btn btn-outline-success" @click="exportExcel">
            <i class="bi bi-file-earmark-excel"></i> 匯出 Excel
          </button>
        </div>
      </div>
    </div>

    <div class="stats-cards mb-4">
      <div class="row">
        <div class="col-md-3">
          <div class="card bg-primary text-white">
            <div class="card-body">
              <h5 class="card-title">銷售總額</h5>
              <h3>NT$ {{ formatCurrency(stats.total_sales) }}</h3>
              <p class="card-text">期間總銷售額</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <h5 class="card-title">交易筆數</h5>
              <h3>{{ stats.transaction_count }}</h3>
              <p class="card-text">成功交易總數</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-warning">
            <div class="card-body">
              <h5 class="card-title">手續費</h5>
              <h3>NT$ {{ formatCurrency(stats.total_fees) }}</h3>
              <p class="card-text">金流手續費</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <h5 class="card-title">淨收入</h5>
              <h3>NT$ {{ formatCurrency(stats.net_income) }}</h3>
              <p class="card-text">扣除手續費後收入</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">每日銷售趨勢</h5>
            <canvas ref="dailySalesChart"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">支付方式分布</h5>
            <canvas ref="paymentMethodsChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <transaction-list
      :startDate="filters.startDate"
      :endDate="filters.endDate"
      :paymentMethod="filters.paymentMethod"
      @view-day-detail="viewDayDetail"
    ></transaction-list>

    <!-- 日交易明細彈窗 -->
    <div class="modal fade" id="dayDetailModal" tabindex="-1" ref="dayDetailModal">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ selectedDate }} 交易明細</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <daily-detail
              v-if="selectedDate"
              :date="selectedDate"
              :paymentMethod="filters.paymentMethod"
            ></daily-detail>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { Modal } from 'bootstrap';
import Chart from 'chart.js/auto';
import TransactionList from './TransactionList.vue';
import DailyDetail from './DailyDetail.vue';
import axios from 'axios';

export default {
  components: {
    TransactionList,
    DailyDetail
  },
  setup() {
    // 參考到圖表元素
    const dailySalesChart = ref(null);
    const paymentMethodsChart = ref(null);
    const dayDetailModal = ref(null);
    
    // 存儲圖表實例
    let salesChart = null;
    let methodsChart = null;
    
    // 篩選器
    const filters = reactive({
      startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().substr(0, 10),
      endDate: new Date().toISOString().substr(0, 10),
      paymentMethod: ''
    });
    
    // 統計資料
    const stats = reactive({
      total_sales: 0,
      transaction_count: 0,
      total_fees: 0,
      net_income: 0
    });
    
    // 圖表資料
    const chartData = reactive({
      dailyStats: [],
      paymentMethods: []
    });
    
    // 選中的日期
    const selectedDate = ref('');
    
    // 載入儀表板資料
    const loadDashboardData = async () => {
      try {
        const response = await axios.get('/api/payments/dashboard', {
          params: {
            start_date: filters.startDate,
            end_date: filters.endDate,
            payment_method: filters.paymentMethod || undefined
          }
        });
        
        // 更新統計資料
        stats.total_sales = response.data.stats.total_sales;
        stats.transaction_count = response.data.stats.transaction_count;
        stats.total_fees = response.data.stats.total_fees;
        stats.net_income = response.data.stats.net_income;
        
        // 更新圖表資料
        chartData.dailyStats = response.data.daily_stats;
        chartData.paymentMethods = response.data.payment_methods;
        
        // 繪製圖表
        renderCharts();
      } catch (error) {
        console.error('載入儀表板資料失敗', error);
      }
    };
    
    // 套用篩選條件
    const applyFilters = () => {
      loadDashboardData();
    };
    
    // 匯出CSV
    const exportCsv = () => {
      const url = `/api/payments/export-csv?start_date=${filters.startDate}&end_date=${filters.endDate}${filters.paymentMethod ? '&payment_method=' + filters.paymentMethod : ''}`;
      window.open(url, '_blank');
    };
    
    // 匯出Excel
    const exportExcel = async () => {
      try {
        const response = await axios.get('/api/payments/export-excel', {
          params: {
            start_date: filters.startDate,
            end_date: filters.endDate,
            payment_method: filters.paymentMethod || undefined
          }
        });
        
        // 前端處理Excel匯出
        const XLSX = require('xlsx');
        const worksheet = XLSX.utils.json_to_sheet(response.data.data);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, '交易記錄');
        XLSX.writeFile(workbook, response.data.filename);
      } catch (error) {
        console.error('匯出Excel失敗', error);
      }
    };
    
    // 繪製圖表
    const renderCharts = () => {
      // 銷售趨勢圖表
      if (salesChart) {
        salesChart.destroy();
      }
      
      if (dailySalesChart.value) {
        const dates = chartData.dailyStats.map(item => item.date);
        const amounts = chartData.dailyStats.map(item => item.total_amount);
        const fees = chartData.dailyStats.map(item => item.total_fee);
        const netAmounts = chartData.dailyStats.map(item => item.total_net_amount);
        
        salesChart = new Chart(dailySalesChart.value, {
          type: 'line',
          data: {
            labels: dates,
            datasets: [
              {
                label: '銷售總額',
                data: amounts,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                fill: true
              },
              {
                label: '手續費',
                data: fees,
                borderColor: 'rgb(255, 159, 64)',
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                fill: true
              },
              {
                label: '淨收入',
                data: netAmounts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: {
                display: true,
                text: '每日銷售趨勢'
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return `${context.dataset.label}: NT$ ${context.parsed.y}`;
                  }
                }
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: '金額 (NT$)'
                }
              },
              x: {
                title: {
                  display: true,
                  text: '日期'
                }
              }
            }
          }
        });
      }
      
      // 支付方式圓餅圖
      if (methodsChart) {
        methodsChart.destroy();
      }
      
      if (paymentMethodsChart.value && chartData.paymentMethods.length > 0) {
        const labels = chartData.paymentMethods.map(item => {
          const methodNames = {
            'credit_card': '信用卡',
            'line_pay': 'Line Pay',
            'bank_transfer': '銀行轉賬',
            'ecpay': '綠界支付'
          };
          return methodNames[item.payment_method] || item.payment_method;
        });
        
        const data = chartData.paymentMethods.map(item => item.total_amount);
        const backgroundColor = [
          'rgba(255, 99, 132, 0.7)',
          'rgba(54, 162, 235, 0.7)',
          'rgba(255, 206, 86, 0.7)',
          'rgba(75, 192, 192, 0.7)',
          'rgba(153, 102, 255, 0.7)'
        ];
        
        methodsChart = new Chart(paymentMethodsChart.value, {
          type: 'pie',
          data: {
            labels: labels,
            datasets: [{
              data: data,
              backgroundColor: backgroundColor
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right',
              },
              title: {
                display: true,
                text: '支付方式分佈'
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: NT$ ${value} (${percentage}%)`;
                  }
                }
              }
            }
          }
        });
      }
    };
    
    // 查看某天的詳細資訊
    const viewDayDetail = (date) => {
      selectedDate.value = date;
      const modal = new Modal(dayDetailModal.value);
      modal.show();
    };
    
    // 格式化貨幣
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('zh-TW').format(value || 0);
    };
    
    // 生命週期鉤子
    onMounted(() => {
      loadDashboardData();
    });
    
    return {
      dailySalesChart,
      paymentMethodsChart,
      dayDetailModal,
      filters,
      stats,
      chartData,
      selectedDate,
      applyFilters,
      exportCsv,
      exportExcel,
      viewDayDetail,
      formatCurrency
    };
  }
};
</script>

<style scoped>
.payment-dashboard {
  padding: 20px;
}

.stats-cards .card {
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.stats-cards .card:hover {
  transform: translateY(-5px);
}

.stats-cards .card-title {
  font-size: 1rem;
  font-weight: 500;
}

.stats-cards h3 {
  font-size: 1.8rem;
  font-weight: 700;
  margin: 10px 0;
}

.stats-cards .card-text {
  font-size: 0.8rem;
  opacity: 0.8;
}

.filters {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}
</style>
