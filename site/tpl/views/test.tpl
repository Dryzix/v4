{*{{var=false}}*}
{*{{v="| lol"}}*}
{*:{{v}}<br/>*}
{*{{tab=["produit","client"]}}*}
{*{{tab}}*}
{*{{tab=["produit" -> "p" ,"client" -> "c"]}}*}
{*{{tab}}*}
{*{if cond="isTrue" param={{var}} /}*}
{*{if cond="isTrue" param={{var}}}*}
    {*{loop i=1000}*}
        {*{rand}0|100{/rand}*}
    {*{/loop}*}
{*{/if}*}

{{var=""}} {{bool=false}}
{if ccond="(notEmpty("+{{var}}+") or isTrue(true)) and isFalse("+{{bool}}+")"}
    Condition vrai
{/if}

<br />
{if ccond="isTrue(false)"}
    Nous somme dans le if
{/if}
{elseif ccond="isFalse(1)"}
Nous somme dans le elseif 1
{/elseif}
{elseif ccond="isFalse(0)"}
Nous somme dans le elseif 2
{/elseif}
{else}
Nous somme dans le else
{/else}
<br />
{*<br />*}

{*{{tables=["t",1,[1,2],3]}}*}

{*{{tables=[*}
            {*"t" -> "test",*}
            {*"p" -> "produit",*}
            {*"j1" -> ["inner" -> "c", "m" -> "messages", "c.idClient = m.idClient"],*}
            {*"j2" -> ["left" -> "p", "c" -> "client", "c.idClient = p.idClient"]*}

        {*]*}
{*}}*}
{*{{select='c.idClient, COUNT(idProduit)'}}*}
{*{{where='t.id="1456"'}}*}
{*{dbSelect in="var" t={{tables}} c={{select}} w={{where}} g="c.idClient" o="p.id DESC" h="COUNT(idProduit) > 2" l="0,20"/}*}
<br />
{{t='eleve'}}
{{c='nom, idClasse'}}
{{v=['Guy', '2']}}
{*{dbInsert t={{t}} c={{c}}  v={{v}}/}*}

{{t='eleve'}}
{{w='id=?'}}
{{v=[13]}}
{*{dbDelete t={{t}} w={{w}} v={{v}} /}*}

{{t='eleve'}}
{{s='nom=:nom'}}
{{v=['nom'->'Jacques', 'id'->12]}}
{{w='id=:id'}}
{dbUpdate t={{t}} s={{s}} v={{v}} w={{w}} /}
<br />
{{t=[
        "c" -> "classe",
        "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{{classe="Terminale"}}
{dbSelect in="var" t={{t}} c="c.libelle, COUNT(*)" w="libelle=:libC" v=['libC' -> '{{classe}}'] /}
{dbLoop for="var"}
    Nom de la classe : {var->libelle}<br />
    Nombre d'élèves : {var->count}<br /><br />
    <strong>IncepLoop : </strong><br />
    {dbSelect in="var2" t={{t}} c="c.libelle, COUNT(*)" w="libelle=:libC" v=['libC' -> '{var->libelle}'] /}
    {dbLoop for="var2"}
        Nom de la classe : {var2->libelle}<br />
        Nombre d'élèves : {var2->count}<br /><br />
    {/dbLoop}
{/dbLoop}
<hr />
{{t=[
    "c" -> "classe",
    "j1" -> ["inner" -> "c", "e"->"eleve", "c.id = e.idClasse"]
    ]
}}
{dbSelect in="var" t={{t}} c="c.id, e.id as idEleve" o="e.id" /}
{dbLoop for="var"}
    Id eleve : {var->idEleve}<br />
    Id classe : {var->id}<br />
    -----------------------------<br/>
{/dbLoop}

{*{{tables}}*}
{*{{where=["t.idClient = p.idClient"]}}*}

{*{db in="req" t={{tables}} w={{where}}/}*}

{*{dbLoop var="req"}*}
    {*{db in="req2" t=["test"] w=["id="+{req(p)->id}+""]/}*}
{*{/dbLoop}*}
