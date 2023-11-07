import { mount } from '@vue/test-utils';
import MonthCalendar from '@/Components/calendar/MonthCalendar.vue';

describe('MonthCalendar.vue', () => {
    let wrapper;

    beforeEach(() => {
        wrapper = mount(MonthCalendar);
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
