select
	pv.dataPagamento,
	pv.tid,
    pv.cliente,
    pv.metodoPagamento,
    pv.parcelas,
    pv.valorPago as 'Valor Venda'
    ,sum(if(r.status = 'paid' and r.entrada >0,1,0)) as 'parcelas pagas'
    /*(select count(pr.id) from pagarme_recebimentos pr where pr.idTransacao = pv.tid and pr.entrada >0) as 'parcelas pagas',
    (select sum(pr.entrada) from pagarme_recebimentos pr where pr.idTransacao = pv.tid) as 'Total Recebimentos PagarMe',
    (select sum(ev.valorCursoCD - ev.credAluno) from erp_vendas ev where ev.tid = pv.tid) as 'Total Vendas ERP'*/
from pagarme_vendas pv
inner join pagarme_recebimentos r
on r.idTransacao = pv.tid
where
pv.status = 'paid' and pv.valorPago <> 0
group by pv.dataPagamento, pv.tid, pv.cliente, pv.metodoPagamento, pv.parcelas, pv.valorPago