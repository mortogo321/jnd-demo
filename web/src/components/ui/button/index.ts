import { cva, type VariantProps } from 'class-variance-authority'

export { default as Button } from './Button.vue'

export const buttonVariants = cva(
  'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-all duration-200 ease-in-out focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:translate-y-0 cursor-pointer hover:scale-[1.02] hover:-translate-y-0.5 active:scale-[0.98] active:translate-y-0',
  {
    variants: {
      variant: {
        default: 'bg-primary text-primary-foreground hover:bg-primary/90 shadow-sm hover:shadow-md disabled:hover:bg-primary disabled:hover:shadow-sm',
        destructive: 'bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-sm hover:shadow-md disabled:hover:bg-destructive disabled:hover:shadow-sm',
        outline: 'border border-input bg-background hover:bg-accent hover:text-accent-foreground hover:border-accent disabled:hover:bg-background',
        secondary: 'bg-secondary text-secondary-foreground hover:bg-secondary/80 shadow-sm hover:shadow-md disabled:hover:bg-secondary disabled:hover:shadow-sm',
        ghost: 'hover:bg-accent hover:text-accent-foreground disabled:hover:bg-transparent',
        link: 'text-primary underline-offset-4 hover:underline disabled:hover:no-underline',
      },
      size: {
        default: 'h-10 px-4 py-2',
        sm: 'h-9 rounded-md px-3 text-xs',
        lg: 'h-11 rounded-md px-8',
        icon: 'h-10 w-10',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  }
)

export type ButtonVariants = VariantProps<typeof buttonVariants>
