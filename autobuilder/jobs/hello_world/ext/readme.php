<div style="font-style: italic; color: gray; padding: 8px 32px 8px 32px;">
<h4>新环境配置注意事项:</h4><div>
	<ul>
		<li>如果配置发布目标是root账户，最好 /home/root 和 /root 目录是同一个</li>
		<li>发布目标 (某些情况下是 /home/$USER) 目录最好处于data目录中，以保证有足够的磁盘空间</li>
	</ul>
</div>
</div>

<h4>内网环境ETCD地址:</h4>
<table class="table table-hover table-bordered table-striped">
<thead>
<tr><th style='text-align:center;' >环境</th><th style='text-align:center;' >类型</th><th style='text-align:center;' >地址</th></tr></thead>
<tbody>
    <tr><td style='text-align:center;' rowspan="6" >内网环境</td><td style='text-align:center;' rowspan="3" >Etcd集群</td><td style='text-align:center;' ><a href='http://10.1.100.30:2379/v2/members' target='_blank' >http://10.1.100.30:2379/v2/members</a></td></tr>
    <tr><td style='text-align:center;' ><a href='http://10.1.100.30:2380/v2/members' target='_blank' >http://10.1.100.30:2380/v2/members</a></td></tr>
    <tr><td style='text-align:center;' ><a href='http://10.1.100.30:2381/v2/members' target='_blank' >http://10.1.100.30:2381/v2/members</a></td></tr>
    <tr><td style='text-align:center;' rowspan="3" >服务分组列表</td><td style='text-align:center;'><a href='http://10.1.100.30:2379/v2/keys/atapp/proxy/services' target='_blank' >http://10.1.100.30:2379/v2/keys/atapp/proxy/services</a></td></tr>
    <tr><td style='text-align:center;'><a href='http://10.1.100.30:2380/v2/keys/atapp/proxy/services' target='_blank' >http://10.1.100.30:2380/v2/keys/atapp/proxy/services</a></td></tr>
    <tr><td style='text-align:center;'><a href='http://10.1.100.30:2381/v2/keys/atapp/proxy/services' target='_blank' >http://10.1.100.30:2381/v2/keys/atapp/proxy/services</a></td></tr>
</table>