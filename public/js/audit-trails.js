// Audit Trails - Quick Filter Functions
function setAuditQuickFilter(period) {
    const today = new Date();
    let fromDate = new Date();
    let toDate = new Date();

    switch(period) {
        case 'today':
            break;
        case 'yesterday':
            fromDate.setDate(today.getDate() - 1);
            toDate.setDate(today.getDate() - 1);
            break;
        case 'week':
            fromDate.setDate(today.getDate() - 7);
            break;
        case 'month':
            fromDate.setMonth(today.getMonth() - 1);
            break;
        case 'year':
            fromDate.setFullYear(today.getFullYear() - 1);
            break;
    }

    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    document.getElementById('auditDateFrom').value = formatDate(fromDate);
    document.getElementById('auditDateTo').value = formatDate(toDate);
    document.getElementById('auditFilterForm').submit();
}
