import { mount } from '@vue/test-utils';
import HistoryCalendar from '@/Components/calendar/HistoryCalendar.vue';

describe('HistoryCalendar.vue', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(HistoryCalendar);
    });

    it('renders the component', () => {
        expect(wrapper.exists()).toBe(true);
    });


    it('has calendar navigation buttons', () => {
        const buttonNames = ['Previous month', 'Next month', 'Go to Today'];
        const buttons = wrapper.findAll('button');

        buttonNames.forEach(name => {
            const button = buttons.find(b => b.text().trim() === name);
            expect(button).toBeTruthy();
        });
    });


});
