select
	pv.dataPagamento,
	pv.tid,
    pv.cliente,
    pv.metodoPagamento,
    pv.parcelas,
    (select count(pr.id) from pagarme_recebimentos pr where pr.idTransacao = pv.tid and pr.entrada >0) as 'parcelas pagas',
    sum(pv.valorPago) as 'Valor Venda',
    
    (select sum(pr.entrada) from pagarme_recebimentos pr where pr.idTransacao = pv.tid) as 'Total Recebimentos PagarMe',
    (select sum(ev.valorCursoCD - ev.credAluno) from erp_vendas ev where ev.tid = pv.tid) as 'Total Vendas ERP'
from pagarme_vendas pv
where
pv.status = 'paid' and pv.valorPago <> 0
/*and pv.dataPagamento < '2020-01-12' and pv.parcelas = 2*/
group by pv.dataPagamento, pv.tid, pv.cliente, pv.metodoPagamento, pv.parcelas