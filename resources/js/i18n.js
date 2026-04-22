import { createI18n } from 'vue-i18n';

import en from '../lang/en.json';
import es from '../lang/es.json';
import fr from '../lang/fr.json';
import de from '../lang/de.json';
import pt_BR from '../lang/pt_BR.json';

const savedLocale = localStorage.getItem('locale') || 'en';

const i18n = createI18n({
    legacy: false, // you must set `false`, to use Composition API
    locale: savedLocale,
    fallbackLocale: 'en',
    messages: {
        en,
        es,
        fr,
        de,
        pt_BR
    }
});

export default i18n;
