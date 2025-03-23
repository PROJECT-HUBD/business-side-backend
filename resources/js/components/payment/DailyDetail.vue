<template>
  <div class="daily-detail">
    <div class="loading-overlay" v-if="loading">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">載入中...</span>
      </div>
    </div>
    
    <div v-else>
      <!-- 每日統計資訊 -->
      <div class="daily-summary mb-4">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-md-9">
                <div class="row">
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">交易日期</small>
                      <strong>{{ formatDate(stats.date) }}</strong>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">交易筆數</small>
                      <strong>{{ stats.transaction_count }}</strong>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">總金額</small>
                      <strong>NT$ {{ formatCurrency(stats.total_amount) }}</strong>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">總手續費</small>
                      <strong>NT$ {{ formatCurrency(stats.total_fee) }}</strong>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">總淨收入</small>
                      <strong>NT$ {{ formatCurrency(stats.total_net_amount) }}</strong>
                    </div>
                  </div>
                  <div class="col-md-3 mb-3">
                    <div class="d-flex flex-column">
                      <small class="text-muted">對帳狀態</small>
                      <span :class="getReconcileStatusClass(stats.reconcile_status)">
                        {{ getReconcileStatusText(stats.reconcile_status) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-3 text-md-end">
                <button 
                  class="btn btn-primary"
                  data-bs-toggle="collapse"
                  data-bs-target="#reconciliationForm"
                  aria-expanded="false"
                >
                  更新對帳狀態
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- 對帳表單 -->
      <div class="collapse mb-4" id="reconciliationForm">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">更新對帳資訊</h5>
            <reconciliation-form 
              :date="date" 
              :current-status="stats.reconcile_status"
              :current-notes="stats.notes"
              @updated="refreshData"
            ></reconciliation-form>
          </div>
        </div>
      </div>
      
      <!-- 交易明細表格 -->
      <div class="transactions-table">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">交易明細</h5>
            <div class="filters">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="搜尋訂單ID或交易ID" v-model="searchQuery">
                <button class="btn btn-outline-secondary" type="button" @click="filterTransactions">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>交易時間</th>
                    <th>交易ID</th>
                    <th>訂單ID</th>
                    <th>支付方式</th>
                    <th>支付閘道</th>
                    <th>金額</th>
                    <th>手續費</th>
                    <th>淨收入</th>
                    <th>交易狀態</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="transaction in filteredTransactions" :key="transaction.id">
                    <td>{{ formatDateTime(transaction.payment_date) }}</td>
                    <td>{{ transaction.transaction_id }}</td>
                    <td>{{ transaction.order_id }}</td>
                    <td>{{ getPaymentMethodName(transaction.payment_method) }}</td>
                    <td>{{ transaction.payment_gateway || 'N/A' }}</td>
                    <td>NT$ {{ formatCurrency(transaction.amount) }}</td>
                    <td>NT$ {{ formatCurrency(transaction.fee) }}</td>
                    <td>NT$ {{ formatCurrency(transaction.net_amount) }}</td>
                    <td>
                      <span :class="getTransactionStatusClass(transaction.status)">
                        {{ getTransactionStatusText(transaction.status) }}
                      </span>
                    </td>
                  </tr>
                  <tr v-if="filteredTransactions.length === 0">
                    <td colspan="9" class="text-center py-4">
                      <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle me-2"></i>沒有符合條件的交易記錄
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import axios from 'axios';
import ReconciliationForm from './ReconciliationForm.vue';

export default {
  components: {
    ReconciliationForm
  },
  
  props: {
    date: {
      type: String,
      required: true
    },
    paymentMethod: {
      type: String,
      default: ''
    }
  },
  
  setup(props) {
    const loading = ref(true);
    const transactions = ref([]);
    const searchQuery = ref('');
    
    const stats = reactive({
      date: '',
      transaction_count: 0,
      total_amount: 0,
      total_fee: 0,
      total_net_amount: 0,
      reconcile_status: 'pending',
      notes: null
    });
    
    // 過濾後的交易記錄
    const filteredTransactions = computed(() => {
      if (!searchQuery.value.trim()) {
        return transactions.value;
      }
      
      const query = searchQuery.value.toLowerCase().trim();
      return transactions.value.filter(t => 
        t.transaction_id.toLowerCase().includes(query) ||
        t.order_id.toLowerCase().includes(query)
      );
    });
    
    // 載入每日交易資料
    const loadDailyData = async () => {
      loading.value = true;
      
      try {
        const response = await axios.get(`/api/payments/daily/${props.date}`, {
          params: {
            payment_method: props.paymentMethod || undefined
          }
        });
        
        // 更新統計資料
        Object.assign(stats, response.data.stats);
        
        // 更新交易清單
        transactions.value = response.data.transactions;
      } catch (error) {
        console.error('載入每日交易資料失敗', error);
      } finally {
        loading.value = false;
      }
    };
    
    // 過濾交易
    const filterTransactions = () => {
      // 過濾已經通過計算屬性完成
    };
    
    // 刷新資料
    const refreshData = () => {
      loadDailyData();
    };
    
    // 格式化日期
    const formatDate = (dateString) => {
      const options = { year: 'numeric', month: '2-digit', day: '2-digit', weekday: 'short' };
      return new Date(dateString).toLocaleDateString('zh-TW', options);
    };
    
    // 格式化日期時間
    const formatDateTime = (dateTimeString) => {
      const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
      return new Date(dateTimeString).toLocaleTimeString('zh-TW', options);
    };
    
    // 格式化貨幣
    const formatCurrency = (value) => {
      return new Intl.NumberFormat('zh-TW').format(value || 0);
    };
    
    // 取得對帳狀態樣式類別
    const getReconcileStatusClass = (status) => {
      switch (status) {
        case 'matched':
          return 'badge bg-success';
        case 'unmatched':
          return 'badge bg-danger';
        default:
          return 'badge bg-warning';
      }
    };
    
    // 取得對帳狀態文字
    const getReconcileStatusText = (status) => {
      switch (status) {
        case 'matched':
          return '已對帳';
        case 'unmatched':
          return '對帳不符';
        default:
          return '待對帳';
      }
    };
    
    // 取得交易狀態樣式類別
    const getTransactionStatusClass = (status) => {
      switch (status) {
        case 'completed':
          return 'badge bg-success';
        case 'failed':
          return 'badge bg-danger';
        case 'pending':
          return 'badge bg-warning';
        default:
          return 'badge bg-secondary';
      }
    };
    
    // 取得交易狀態文字
    const getTransactionStatusText = (status) => {
      switch (status) {
        case 'completed':
          return '完成';
        case 'failed':
          return '失敗';
        case 'pending':
          return '處理中';
        default:
          return status;
      }
    };
    
    // 取得支付方式名稱
    const getPaymentMethodName = (method) => {
      const methodNames = {
        'credit_card': '信用卡',
        'line_pay': 'Line Pay',
        'bank_transfer': '銀行轉賬',
        'ecpay': '綠界支付'
      };
      return methodNames[method] || method;
    };
    
    // 監聽日期變化
    watch(() => props.date, () => {
      loadDailyData();
    });
    
    // 監聽支付方式變化
    watch(() => props.paymentMethod, () => {
      loadDailyData();
    });
    
    // 組件掛載時載入資料
    onMounted(() => {
      loadDailyData();
    });
    
    return {
      loading,
      transactions,
      filteredTransactions,
      stats,
      searchQuery,
      filterTransactions,
      refreshData,
      formatDate,
      formatDateTime,
      formatCurrency,
      getReconcileStatusClass,
      getReconcileStatusText,
      getTransactionStatusClass,
      getTransactionStatusText,
      getPaymentMethodName
    };
  }
};
</script>

<style scoped>
.daily-detail {
  position: relative;
}

.loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10;
}

.daily-summary {
  background-color: #f8f9fb;
  border-radius: 8px;
}

.daily-summary .col-md-3 {
  padding: 10px;
}
</style>
