<!--
 - @copyright Copyright (c) 2022 Andrey Borysenko <andrey18106x@gmail.com>
 -
 - @copyright Copyright (c) 2022 Alexander Piskun <bigcat88@icloud.com>
 -
 - @author 2022 Andrey Borysenko <andrey18106x@gmail.com>
 -
 - @license AGPL-3.0-or-later
 -
 - This program is free software: you can redistribute it and/or modify
 - it under the terms of the GNU Affero General Public License as
 - published by the Free Software Foundation, either version 3 of the
 - License, or (at your option) any later version.
 -
 - This program is distributed in the hope that it will be useful,
 - but WITHOUT ANY WARRANTY; without even the implied warranty of
 - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 - GNU Affero General Public License for more details.
 -
 - You should have received a copy of the GNU Affero General Public License
 - along with this program. If not, see <http://www.gnu.org/licenses/>.
 -
 -->

<template>
	<NcContent app-name="watermark">
		<NcAppNavigation>
			<template #list>
				<NcAppNavigationItem :to="{name: 'watermark'}"
					:title="t('watermak', 'Home')"
					icon="icon-toggle-filelist"
					exact />
			</template>
		</NcAppNavigation>
		<NcAppContent>
			<router-view v-show="!loading" :loading.sync="loading" />
		</NcAppContent>
	</NcContent>
</template>

<script>
import NcContent from '@nextcloud/vue/dist/Components/NcContent.js'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent.js'
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation.js'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem.js'

export default {
	name: 'Watermark',
	components: {
		NcContent,
		NcAppContent,
		NcAppNavigation,
		NcAppNavigationItem,
	},
	data() {
		return {
			loading: true,
		}
	},
}
</script>
<style scoped>
h1 {
	text-align: center;
	margin: 10px auto;
}
</style>
