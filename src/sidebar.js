/**
 * Internal block libraries
 */

const { __ } = wp.i18n;

const {
	PluginSidebar,
	PluginSidebarMoreMenuItem
} = wp.editPost;

const {
	PanelBody,
	TextControl,
	DateTimePicker,
	SelectControl
} = wp.components;

const {
	__experimentalGetSettings
} = wp.date;

const {
	Component,
	Fragment
} = wp.element;

const { withSelect } = wp.data;

const { compose, withState } = wp.compose;

const { registerPlugin } = wp.plugins;

class Seorefresh_Link extends Component {
	
	constructor() {
		super( ...arguments );

		//Default
		const fields  = [
			{
				key: '_seorefresh_link_field',
				value: new Date(),
			},
			{
				key: '_seorefresh_link_field_checker',
			  	value: '',
			}
		  ];

		
		//Definizioni State
		this.state = {
			fields: fields,
		}
		
		console.log("Wp Seo Refresh Links init OK");
		
		//Fetch Iniziale
		wp.apiFetch( { path: `/wp/v2/posts/${this.props.postId}`, method: 'GET' } ).then(
			( data ) => {

				//Array con dati aggiornati
				const fields_updated  = [
					{
						key: '_seorefresh_link_field',
						value: data.meta._seorefresh_link_field,
					},
					{
						key: '_seorefresh_link_field_checker',
						value: data.meta._seorefresh_link_field_checker,
					}
				  ];

				//Aggiorna
				this.setState( { 
					fields: fields_updated,
				} );

				return data;
			},
			( err ) => {
				return err;
			}
		);// Fetch iniziale

	}//constructor

	//Save Action
	static getDerivedStateFromProps( nextProps, state ) {
		//Ref: https://blog.logrocket.com/the-new-react-lifecycle-methods-in-plain-approachable-language-61a2105859f3/
		

		//Se si sta pubblicando / salvando
		if ( ( nextProps.isPublishing || nextProps.isSaving ) && !nextProps.isAutoSaving ) {
			//console.log(state.fields)
			//console.log("Saving/Rendering   "+JSON.stringify(state));
			
			for (let index = 0; index < state.fields.length; index++) {
				
				wp.apiRequest( { path: `/seorefreshlink-gutenberg/v1/update-meta?id=${nextProps.postId}`, method: 'POST', data: state.fields[index] } ).then(
					( data ) => {
						//console.log(data);
						return data;
					},
					( err ) => {
						return err;
					}
				);
				
			}//for

		}//Se si sta pubblicando / salvando
	}//getDerivedStateFromProps

	//OnChange - Date
	onDateChange(newvalue) {

		var stateCopy = Object.assign({}, this.state);
		stateCopy.fields[0].value = newvalue;
		this.setState(stateCopy);
		
		//this.state.fields[0].value = newvalue;
		
	}

	//OnChange - Checker
	onCheckerChange(newchecker) {
		var stateCopy = Object.assign({}, this.state);
		stateCopy.fields[1].value = newchecker;
		this.setState(stateCopy);
	}
	
	//Render
	render() {
		
		//hard bind
		let _this = this;
		
			//Essential per DataTimePicker
			const settings = __experimentalGetSettings();
			// To know if the current timezone is a 12 hour time with look for an "a" in the time format.
			// We also make sure this a is not escaped by a "/".
			const is12HourTime = /a(?!\\)/i.test(
				settings.formats.time
					.toLowerCase() // Test only the lower case a
					.replace( /\\\\/g, '' ) // Replace "//" with empty strings
					.split( '' ).reverse().join( '' ) // Reverse the string and test for "a" not followed by a slash
			);//Essential per DataTimePicker


			//Debug for errors
			console.log(moment(_this.state.fields[0].value, "YYYY-MM-DD hh:mm").format('DD-MM-YYYY hh:mm'));
			console.log(_this.state.fields[0].value);
			console.log(_this.state.fields[1].value);

		
		//Return
		return (
			<Fragment>
				<PluginSidebar
					name="seorefresh-link-sidebar"
					title={ __( 'Seo Refresh Link' ) }
					>
					<PanelBody>

						<SelectControl
							label="Attivare la ripubblicazione?"
							value={ this.state.fields[1].value }
							options={ [
								{ label: 'No', value: 'No' },
								{ label: 'Si', value: 'Si' },
							] }
							onChange={ ( checker ) => { this.onCheckerChange(checker) }}
							
						/>

						
						<DateTimePicker
							currentDate={ moment(_this.state.fields[0].value, "YYYY-MM-DD hh:mm").isValid() && moment(_this.state.fields[0].value, "YYYY-MM-DD hh:mm").format('YYYY-MM-DD HH:mm') }
							onChange={ ( value ) => { this.onDateChange(value) }}
							is12Hour={ is12HourTime }
						/>

				
					</PanelBody>
				</PluginSidebar>
			</Fragment>
		)
	}
}// End Render

//Costruttore
const HOC = withSelect( ( select, { forceIsSaving } ) => {
	const {
		getCurrentPostId,
		isSavingPost,
		isPublishingPost,
		isAutosavingPost,
	} = select( 'core/editor' );
	return {
		postId: getCurrentPostId(),
		isSaving: forceIsSaving || isSavingPost(),
		isAutoSaving: isAutosavingPost(),
		isPublishing: isPublishingPost(),
	};
} )( Seorefresh_Link );

//Register Gutemberg Plugin
registerPlugin( 'seorefresh-link', {
	icon: 'controls-repeat',
	render: HOC,
} );