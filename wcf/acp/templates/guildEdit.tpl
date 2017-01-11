﻿{include file='header' pageTitle='{lang}wcf.acp.gman.guild.edit{/lang} Gilde bearbeiten'}

{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
	<script data-relocate="true">
		{include file='mediaJavaScript'}
		
		require(['WoltLabSuite/Core/Media/Manager/Select'], function(MediaManagerSelect) {
			new MediaManagerSelect({
				dialogTitle: '{lang}wcf.media.chooseImage{/lang}',
				imagesOnly: 1
			});
		});
	</script>
{/if}

<header class="contentHeader">
	<div class="contentHeaderTitle">
	  <h1 class="contentTitle">{lang}wcf.acp.gman.guild{/lang} bearbeiten</h1>
	</div>
</header>

{include file='formError'}
    {if $success|isset}
        <p class="success">{lang}wcf.global.success.{$action}{/lang}</p>
    {/if}
    
    {if $firstjob|isset}
        <p class="success">{lang}wcf.acp.gman.guild.datasuccess{/lang}</p>
    {/if}


<form id="guildEditForm" method="post" action="{link controller='GuildEdit'}{/link}" enctype="multipart/form-data">
	<div class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.bnetdata{/lang}</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.bnetdata.desc{/lang}</p>
		</header>
		<dl>
			<dt>{lang}wcf.acp.gman.guild.name{/lang}</dt>
			<dd>
				<p>{$guild->name} ({$guild->side})</p>
                <small>{lang}wcf.acp.gman.guild.name.desc{/lang}</small>
			</dd>
		</dl>  
		<dl>
			<dt>{lang}wcf.acp.gman.guild.realm Realm{/lang}</dt>
			<dd>
				<p>{$guild->getRealm()->name} 
                {if $guild->getRealm()->getConnetedRealmCount()>0}
                    <br />({foreach from=$guild->getRealm()->getConnectedRealms() item=$realm name=realm}
                        {if !($tpl.foreach.realm.last == $realm)}{$realm->name},{else}{$realm->name}{/if}
                    {/foreach})
                 <small>{lang}wcf.page.gman.guild.realm.connected{/lang}</small>
                {/if}
               
            </p>
                <small>{lang}wcf.acp.gman.guild.realm.desc{/lang}</small>
			</dd>
		</dl>  
		<dl>
			<dt>{lang}wcf.page.gman.guildAchievementPoints{/lang}</dt>
			<dd>
				<p>{$guild->achievementPoints}</p>
                <small>{lang}wcf.page.gman.guildAchievementPoints.desc{/lang}</small>
			</dd>
		</dl>     
        <dl>
			<dt>{lang}wcf.acp.page.lastUpdateTime{/lang}</dt>
			<dd>
				<p>{$guild->lastModified} / </p>
                <small>{lang}wcf.acp.gman.lastChangeAndUpdate{/lang}</small>
			</dd>
		</dl>  
        <dl>
			<dt>{lang}wcf.page.gman.guildleader{/lang}</dt>
			<dd>
                {assign var=glead value=$guild->getLeader()}
                <div class="box48">
                    <div>{@$glead->getAvatar()->getImageTag(48)}</div>
                    <div class="details userInformation">
                        <div class="containerHeadline">
                            <h3>{$glead->name}</h3>
                            <p>
                                <small class="separatorLeft"><span>{$glead->level}</span>, <span>{$glead->race}</span>, <span>{$glead->class}</span></small>
                            </p>
                        </div>

                    </div>
                </div>
				<p>{$guild->getLeader()->charname}</p> 
                <small>{lang}wcf.page.gman.guildleader.desc{/lang}</small>
			</dd>
		</dl>                                        
     </div>
     <div class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.guildinfo{/lang}</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.guildinfo.desc{/lang}</p>
		</header>
			{if $__wcf->session->getPermission('admin.content.cms.canUseMedia')}
				<dl{if $errorField == 'image'} class="formError"{/if}>
					<dt><label for="image">{lang}wcf.acp.gman.guild.image{/lang}</label></dt>
					<dd>
						<div id="imageDisplay" class="selectedImagePreview">
							{if $images[0]|isset}
								{@$images[0]->getThumbnailTag('small')}
							{/if}
						</div>
						<p class="button jsMediaSelectButton" data-store="imageID0" data-display="imageDisplay">{lang}wcf.media.chooseImage{/lang}</p>
						<input type="hidden" name="imageID[0]" id="imageID0"{if $imageID[0]|isset} value="{@$imageID[0]}"{/if}>
						{if $errorField == 'image'}
							<small class="innerError">{lang}wcf.acp.article.image.error.{@$errorType}{/lang}</small>
						{/if}
					</dd>
				</dl>
			{elseif $action == 'edit' && $images[0]|isset}
				<dl>
					<dt>{lang}wcf.acp.article.image{/lang}</dt>
					<dd>
						<div id="imageDisplay">{@$images[0]->getThumbnailTag('small')}</div>
					</dd>
				</dl>
			{/if}    
		    <dl{if $errorField == 'birthdayDate'} class="formError"{/if}>
			    <dt><label for="birthdayDate">{lang}wcf.acp.gman.guild.birthdate{/lang}</label></dt>
			    <dd>
				    <input type="datetime" id="birthdayDate" name="birthdayDate" value="{$birthday}" class="medium">
				    {if $errorField == 'birthdayDate'}
					    <small class="innerError">
						    {if $errorType == 'empty'}
							    {lang}wcf.global.form.error.empty{/lang}
						    {else}
							    {lang}wcf.acp.article.time.error.{@$errorType}{/lang}
						    {/if}
					    </small>
				    {/if}
			    </dd>
		    </dl>
         <dl{if $errorField=='articleID'} class="formError" {/if}>
             <dt><label for="articleID">{lang}wcf.acp.gman.guild.article{/lang}</label></dt>
             <dd>
                 <select name="articleID" id="articleID">
                     <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                     {foreach from=$articles item=$article}
                     <option value="{$article->articleID}" {if $article->articleID == $articleID} selected{/if}>{$article->getTitle()}</option>
                     {/foreach}
                 </select>
                 {if $errorField == 'articleID'}
                 <small class="innerError">
                     {if $errorType == 'empty'}
                     {lang}wcf.global.form.error.empty{/lang}
                     {else}
                     {lang}wcf.acp.article.category.error.{@$errorType}{/lang}
                     {/if}
                 </small>
                 {/if}
             </dd>
         </dl>
         <dl{if $errorField=='pageID'} class="formError" {/if}>
         <dt><label for="pageID">{lang}wcf.acp.gman.guild.article{/lang}</label></dt>
         <dd>
             <select name="pageID" id="pageID">
                 <option value="0">{lang}wcf.global.noSelection{/lang}</option>

                 {foreach from=$pages item=$page}
                 <option value="{$page->pageID}" {if $page->pageID == $pageID} selected{/if}>{$page->getTitle()}</option>
                 {/foreach}
             </select>             
             {if $errorField == 'pageID'}
             <small class="innerError">
                 {if $errorType == 'empty'}
                 {lang}wcf.global.form.error.empty{/lang}
                 {else}
                 {lang}wcf.user.username.error.{@$errorType}{/lang}
                 {/if}
             </small>
             {/if}
         </dd>
         </dl
    </div>

 	<div class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}wcf.acp.gman.guild.data{/lang}</h2>
			<p class="sectionDescription">{lang}wcf.acp.gman.guild.data.desc{/lang}</p>
		</header>
		<dl>
			<dt>{lang}wcf.page.gman.guildmember{/lang} Gildenmember</dt>
			<dd>
				[BUTTON GILDENMEMBER]
			</dd>
		</dl>    
		<dl>
			<dt>{lang}wcf.page.guildacm{/lang} Gildenerfolge</dt>
			<dd>
				[BUTTON GILDENACMS]
			</dd>
		</dl>        
		<dl>
			<dt>{lang}wcf.acp.gman.guild.data.sync{/lang}</dt>
			<dd>
				<label><input name="autosync" type="checkbox" id="autosync" value="0">{lang}wcf.acp.gman.guild.data.sync.desc{/lang}</label>
			</dd>
		</dl>
	</div>
	<div class="formSubmit">
    	<input type="hidden">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{@SECURITY_TOKEN_INPUT_TAG}
	</div>
</form>

{include file='footer'}