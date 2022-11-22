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
	<div class="container" style="text-align: center;">
		<h1>{{ rootTitle }}</h1>
		<div class="python-test">
			<NcButton :disabled="requesting"
				style="margin: 10px auto;"
				@click="requestPythonRun">
				<template #default>
					{{ t('watermark', 'Request Python check') }}
				</template>
				<template v-if="requesting" #icon>
					<span class="icon-loading-small" />
				</template>
			</NcButton>
			<div v-if="pythonOutput !== null" class="python-output">
				<pre>{{ pythonOutput }}</pre>
			</div>
		</div>
	</div>
</template>

<script>
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

export default {
	name: 'Home',
	components: {
		NcButton,
	},
	props: {
		rootTitle: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			requesting: false,
			pythonOutput: null,
		}
	},
	beforeMount() {
		this.$emit('update:loading', false)
	},
	methods: {
		requestPythonRun() {
			this.requesting = true
			axios.get(generateUrl('/apps/watermark/api/v1/python/check')).then(res => {
				this.pythonOutput = res.data
				this.requesting = false
			}).catch(err => {
				console.debug(err)
				showError(this.t('watermark', 'A server error occurred'))
				this.requesting = false
			})
		},
	},
}
</script>
<style scoped>
.container {
	width: 100%;
	max-width: 1200px;
	margin: 0 auto;
	padding: 20px 10px;
}

h1 {
	text-align: center;
	margin: 10px auto;
	font-size: 24px;
	font-weight: bold;
}
</style>
